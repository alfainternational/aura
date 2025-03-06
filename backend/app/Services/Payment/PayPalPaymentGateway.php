<?php

namespace App\Services\Payment;

use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalPaymentGateway implements PaymentGatewayInterface
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string|null
     */
    protected $accessToken;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->clientId = config('payment.paypal.client_id');
        $this->clientSecret = config('payment.paypal.client_secret');
        $this->baseUrl = config('payment.paypal.environment') === 'production'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    /**
     * إنشاء معاملة دفع جديدة
     *
     * @param Order $order
     * @param array $paymentData
     * @return array بيانات معاملة الدفع بما في ذلك رابط الدفع أو رمز المعاملة
     */
    public function createTransaction(Order $order, array $paymentData = []): array
    {
        try {
            $this->getAccessToken();

            $returnUrl = $paymentData['return_url'] ?? route('commerce.payments.callback', ['gateway' => 'paypal', 'payment_id' => 0]);
            $cancelUrl = $paymentData['cancel_url'] ?? route('commerce.payments.cancel', ['gateway' => 'paypal', 'payment_id' => 0]);

            // Crear una orden de PayPal
            $response = Http::withToken($this->accessToken)
                ->post("{$this->baseUrl}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'reference_id' => $order->order_number,
                            'description' => "Pedido #{$order->order_number}",
                            'amount' => [
                                'currency_code' => strtoupper($order->currency ?? 'USD'),
                                'value' => number_format($order->total, 2, '.', ''),
                                'breakdown' => [
                                    'item_total' => [
                                        'currency_code' => strtoupper($order->currency ?? 'USD'),
                                        'value' => number_format($order->subtotal ?? $order->total, 2, '.', ''),
                                    ],
                                    'shipping' => [
                                        'currency_code' => strtoupper($order->currency ?? 'USD'),
                                        'value' => number_format($order->shipping ?? 0, 2, '.', ''),
                                    ],
                                    'tax_total' => [
                                        'currency_code' => strtoupper($order->currency ?? 'USD'),
                                        'value' => number_format($order->tax ?? 0, 2, '.', ''),
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'application_context' => [
                        'brand_name' => config('app.name'),
                        'landing_page' => 'BILLING',
                        'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                        'user_action' => 'PAY_NOW',
                        'return_url' => $returnUrl,
                        'cancel_url' => $cancelUrl,
                    ],
                ]);

            if ($response->successful()) {
                $paypalOrder = $response->json();
                $approveLink = collect($paypalOrder['links'])->firstWhere('rel', 'approve')['href'] ?? null;

                if (!$approveLink) {
                    throw new \Exception('No se encontró el enlace de aprobación de PayPal');
                }

                return [
                    'success' => true,
                    'transaction_id' => $paypalOrder['id'],
                    'payment_url' => $approveLink,
                    'status' => $paypalOrder['status'],
                    'amount' => $order->total,
                    'currency' => $order->currency ?? 'USD',
                ];
            } else {
                $error = $response->json();
                Log::error('PayPal order creation failed', [
                    'order_id' => $order->id,
                    'error' => $error,
                ]);

                return [
                    'success' => false,
                    'error' => $error['message'] ?? 'Error al crear la orden de PayPal',
                ];
            }
        } catch (\Exception $e) {
            Log::error('PayPal payment creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * التحقق من حالة معاملة دفع
     *
     * @param string $transactionId
     * @return array معلومات حالة معاملة الدفع
     */
    public function checkTransactionStatus(string $transactionId): array
    {
        try {
            $this->getAccessToken();

            $response = Http::withToken($this->accessToken)
                ->get("{$this->baseUrl}/v2/checkout/orders/{$transactionId}");

            if ($response->successful()) {
                $paypalOrder = $response->json();
                $status = 'pending';
                $success = false;

                switch ($paypalOrder['status']) {
                    case 'COMPLETED':
                    case 'APPROVED':
                        $status = 'completed';
                        $success = true;
                        break;
                    case 'SAVED':
                    case 'CREATED':
                    case 'PAYER_ACTION_REQUIRED':
                        $status = 'pending';
                        break;
                    case 'VOIDED':
                        $status = 'canceled';
                        break;
                    default:
                        $status = 'failed';
                }

                return [
                    'success' => $success,
                    'status' => $status,
                    'transaction_id' => $transactionId,
                    'amount' => $paypalOrder['purchase_units'][0]['amount']['value'] ?? 0,
                    'currency' => $paypalOrder['purchase_units'][0]['amount']['currency_code'] ?? 'USD',
                    'details' => [
                        'paypal_status' => $paypalOrder['status'],
                    ],
                ];
            } else {
                $error = $response->json();
                Log::error('PayPal order status check failed', [
                    'transaction_id' => $transactionId,
                    'error' => $error,
                ]);

                return [
                    'success' => false,
                    'status' => 'error',
                    'error' => $error['message'] ?? 'Error al verificar el estado de la orden de PayPal',
                ];
            }
        } catch (\Exception $e) {
            Log::error('PayPal payment status check failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * تأكيد عملية الدفع بعد اكتمالها
     *
     * @param Payment $payment
     * @param array $data
     * @return bool نجاح أو فشل تأكيد عملية الدفع
     */
    public function confirmPayment(Payment $payment, array $data = []): bool
    {
        try {
            $this->getAccessToken();

            // Capturar el pago de PayPal
            $response = Http::withToken($this->accessToken)
                ->post("{$this->baseUrl}/v2/checkout/orders/{$payment->transaction_id}/capture");

            if ($response->successful()) {
                $captureData = $response->json();

                // Actualizar el registro de pago
                $payment->status = 'completed';
                $payment->payment_details = array_merge($payment->payment_details ?? [], [
                    'payer_id' => $captureData['payer']['payer_id'] ?? null,
                    'payer_email' => $captureData['payer']['email_address'] ?? null,
                    'capture_id' => $captureData['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                ]);
                $payment->save();

                return true;
            } else {
                $error = $response->json();
                Log::error('PayPal payment capture failed', [
                    'payment_id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'error' => $error,
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error('PayPal payment confirmation failed', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * إلغاء معاملة دفع
     *
     * @param Payment $payment
     * @return bool نجاح أو فشل إلغاء معاملة الدفع
     */
    public function cancelTransaction(Payment $payment): bool
    {
        try {
            // Para PayPal, simplemente marcamos el pago como cancelado
            // ya que no hay una API específica para cancelar órdenes pendientes
            $payment->status = 'canceled';
            $payment->save();

            return true;
        } catch (\Exception $e) {
            Log::error('PayPal payment cancellation failed', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * طلب استرداد مبلغ عملية دفع
     *
     * @param Payment $payment
     * @param float|null $amount المبلغ المراد استرداده، إذا كان null فسيتم استرداد كامل المبلغ
     * @param string $reason سبب الاسترداد
     * @return array معلومات عملية الاسترداد
     */
    public function refundTransaction(Payment $payment, ?float $amount = null, string $reason = ''): array
    {
        try {
            $this->getAccessToken();

            // Obtener el ID de captura del pago
            $captureId = $payment->payment_details['capture_id'] ?? null;

            if (!$captureId) {
                return [
                    'success' => false,
                    'error' => 'No se encontró el ID de captura para este pago',
                ];
            }

            $refundData = [
                'note_to_payer' => $reason ?: 'Reembolso solicitado',
            ];

            // Si se especifica un monto, incluirlo en la solicitud
            if ($amount !== null) {
                $refundData['amount'] = [
                    'value' => number_format($amount, 2, '.', ''),
                    'currency_code' => strtoupper($payment->currency ?? 'USD'),
                ];
            }

            // Realizar el reembolso
            $response = Http::withToken($this->accessToken)
                ->post("{$this->baseUrl}/v2/payments/captures/{$captureId}/refund", $refundData);

            if ($response->successful()) {
                $refundData = $response->json();

                // Actualizar el estado del pago
                $payment->status = $amount === null ? 'refunded' : 'partially_refunded';
                $payment->payment_details = array_merge($payment->payment_details ?? [], [
                    'refund_id' => $refundData['id'],
                    'refund_amount' => $amount ?? $payment->amount,
                    'refund_reason' => $reason,
                    'refund_date' => now()->toIso8601String(),
                ]);
                $payment->save();

                return [
                    'success' => true,
                    'refund_id' => $refundData['id'],
                    'amount' => $amount ?? $payment->amount,
                    'currency' => $payment->currency,
                    'status' => $refundData['status'],
                ];
            } else {
                $error = $response->json();
                Log::error('PayPal refund failed', [
                    'payment_id' => $payment->id,
                    'capture_id' => $captureId,
                    'error' => $error,
                ]);

                return [
                    'success' => false,
                    'error' => $error['message'] ?? 'Error al procesar el reembolso',
                ];
            }
        } catch (\Exception $e) {
            Log::error('PayPal refund failed', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * معالجة إشعار الويب هوك من بوابة الدفع
     *
     * @param array $data بيانات الويب هوك
     * @return array معلومات حول معالجة الويب هوك
     */
    public function handleWebhook(array $data): array
    {
        try {
            $eventType = $data['event_type'] ?? '';

            switch ($eventType) {
                case 'PAYMENT.CAPTURE.COMPLETED':
                    return $this->handlePaymentCaptureCompleted($data);

                case 'PAYMENT.CAPTURE.REFUNDED':
                    return $this->handlePaymentCaptureRefunded($data);

                default:
                    return [
                        'success' => true,
                        'action' => 'ignored',
                        'message' => 'Evento no procesado: ' . $eventType,
                    ];
            }
        } catch (\Exception $e) {
            Log::error('PayPal webhook processing error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * الحصول على اسم بوابة الدفع
     *
     * @return string اسم بوابة الدفع
     */
    public function getName(): string
    {
        return 'PayPal';
    }

    /**
     * الحصول على خيارات الدفع المتاحة في هذه البوابة
     *
     * @return array خيارات الدفع المتاحة
     */
    public function getPaymentOptions(): array
    {
        return [
            'paypal' => [
                'name' => 'PayPal',
                'icon' => 'paypal',
            ],
        ];
    }

    /**
     * التحقق مما إذا كانت البوابة تدعم الدفع بالأقساط
     *
     * @return bool
     */
    public function supportsInstallments(): bool
    {
        return false;
    }

    /**
     * الحصول على خطط الأقساط المتاحة
     *
     * @param float $amount المبلغ المراد تقسيطه
     * @return array خطط الأقساط المتاحة
     */
    public function getInstallmentPlans(float $amount): array
    {
        return [];
    }

    /**
     * Obtener un token de acceso de PayPal
     *
     * @return void
     * @throws \Exception
     */
    protected function getAccessToken(): void
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post("{$this->baseUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                $this->accessToken = $response->json()['access_token'];
            } else {
                throw new \Exception('Error al obtener el token de acceso de PayPal: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('PayPal access token error', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Manejar evento de captura de pago completada
     *
     * @param array $data
     * @return array
     */
    protected function handlePaymentCaptureCompleted(array $data): array
    {
        $resourceId = $data['resource']['id'] ?? null;
        
        if (!$resourceId) {
            return [
                'success' => false,
                'error' => 'No se encontró el ID del recurso',
            ];
        }

        // Buscar el pago por el ID de captura
        $payment = Payment::whereJsonContains('payment_details->capture_id', $resourceId)->first();

        if ($payment) {
            // Confirmar el pago
            $payment->status = 'completed';
            $payment->save();

            // Actualizar el estado del pedido
            $order = $payment->order;
            $order->status = 'processing';
            $order->payment_status = 'completed';
            $order->save();

            return [
                'success' => true,
                'action' => 'payment_confirmed',
                'transaction_id' => $payment->transaction_id,
                'order_id' => $payment->order_id,
            ];
        }

        return [
            'success' => false,
            'error' => 'No se encontró el registro de pago',
        ];
    }

    /**
     * Manejar evento de reembolso de captura de pago
     *
     * @param array $data
     * @return array
     */
    protected function handlePaymentCaptureRefunded(array $data): array
    {
        $resourceId = $data['resource']['id'] ?? null;
        
        if (!$resourceId) {
            return [
                'success' => false,
                'error' => 'No se encontró el ID del recurso',
            ];
        }

        // Buscar el pago por el ID de captura
        $payment = Payment::whereJsonContains('payment_details->capture_id', $resourceId)->first();

        if ($payment) {
            $isFullRefund = $data['resource']['amount']['value'] == $payment->amount;
            $payment->status = $isFullRefund ? 'refunded' : 'partially_refunded';
            $payment->payment_details = array_merge($payment->payment_details ?? [], [
                'refund_id' => $data['resource']['links'][1]['href'] ?? null,
                'refund_amount' => $data['resource']['amount']['value'] ?? null,
                'refund_date' => now()->toIso8601String(),
            ]);
            $payment->save();

            return [
                'success' => true,
                'action' => 'payment_refunded',
                'transaction_id' => $payment->transaction_id,
            ];
        }

        return [
            'success' => false,
            'error' => 'No se encontró el registro de pago',
        ];
    }
}
