<?php

namespace App\Services\Payment;

use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\StripeClient;

class StripePaymentGateway implements PaymentGatewayInterface
{
    /**
     * @var StripeClient
     */
    protected $stripeClient;

    /**
     * Constructor
     */
    public function __construct()
    {
        Stripe::setApiKey(config('payment.stripe.secret_key'));
        $this->stripeClient = new StripeClient(config('payment.stripe.secret_key'));
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
            // Crear un PaymentIntent de Stripe
            $paymentIntent = $this->stripeClient->paymentIntents->create([
                'amount' => $this->convertToCents($order->total),
                'currency' => strtolower($order->currency ?? 'usd'),
                'payment_method_types' => ['card'],
                'description' => "Pedido #{$order->order_number}",
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_id' => $order->user_id,
                ],
            ]);

            return [
                'success' => true,
                'transaction_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'public_key' => config('payment.stripe.public_key'),
                'amount' => $order->total,
                'currency' => $order->currency ?? 'USD',
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment creation failed', [
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
            $paymentIntent = $this->stripeClient->paymentIntents->retrieve($transactionId);

            $status = 'pending';
            $success = false;

            switch ($paymentIntent->status) {
                case 'succeeded':
                    $status = 'completed';
                    $success = true;
                    break;
                case 'processing':
                    $status = 'processing';
                    break;
                case 'requires_payment_method':
                case 'requires_confirmation':
                case 'requires_action':
                case 'requires_capture':
                    $status = 'pending';
                    break;
                case 'canceled':
                    $status = 'canceled';
                    break;
                default:
                    $status = 'failed';
            }

            return [
                'success' => $success,
                'status' => $status,
                'transaction_id' => $transactionId,
                'amount' => $this->convertFromCents($paymentIntent->amount),
                'currency' => strtoupper($paymentIntent->currency),
                'payment_method' => $paymentIntent->payment_method_types[0] ?? 'unknown',
                'details' => [
                    'stripe_status' => $paymentIntent->status,
                ],
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment status check failed', [
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
            // Verificar el estado actual del PaymentIntent
            $paymentIntent = $this->stripeClient->paymentIntents->retrieve($payment->transaction_id);

            if ($paymentIntent->status === 'succeeded') {
                // Actualizar el registro de pago
                $payment->status = 'completed';
                $payment->payment_details = array_merge($payment->payment_details ?? [], [
                    'payment_method_id' => $paymentIntent->payment_method,
                    'payment_method_details' => $paymentIntent->charges->data[0]->payment_method_details ?? null,
                    'receipt_url' => $paymentIntent->charges->data[0]->receipt_url ?? null,
                ]);
                $payment->save();

                return true;
            }

            return false;
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment confirmation failed', [
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
            if ($payment->transaction_id) {
                $paymentIntent = $this->stripeClient->paymentIntents->retrieve($payment->transaction_id);

                // Solo se puede cancelar si no está completado o ya cancelado
                if (!in_array($paymentIntent->status, ['succeeded', 'canceled'])) {
                    $this->stripeClient->paymentIntents->cancel($payment->transaction_id);
                }
            }

            // Actualizar el registro de pago
            $payment->status = 'canceled';
            $payment->save();

            return true;
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment cancellation failed', [
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
            // Obtener la información de cargo desde el PaymentIntent
            $paymentIntent = $this->stripeClient->paymentIntents->retrieve($payment->transaction_id);
            
            if (empty($paymentIntent->charges->data)) {
                return [
                    'success' => false,
                    'error' => 'No se encontró información de cargo para este pago',
                ];
            }

            $chargeId = $paymentIntent->charges->data[0]->id;
            
            // Crear el reembolso
            $refundData = [
                'charge' => $chargeId,
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'reason' => $reason,
                ],
            ];

            // Si se especifica un monto, convertirlo a centavos
            if ($amount !== null) {
                $refundData['amount'] = $this->convertToCents($amount);
            }

            $refund = $this->stripeClient->refunds->create($refundData);

            // Actualizar el estado del pago
            $payment->status = $amount === null ? 'refunded' : 'partially_refunded';
            $payment->payment_details = array_merge($payment->payment_details ?? [], [
                'refund_id' => $refund->id,
                'refund_amount' => $amount ?? $payment->amount,
                'refund_reason' => $reason,
                'refund_date' => now()->toIso8601String(),
            ]);
            $payment->save();

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $amount ?? $payment->amount,
                'currency' => $payment->currency,
                'status' => $refund->status,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe refund failed', [
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
            $payload = $data['payload'] ?? '';
            $sigHeader = $data['signature'] ?? '';
            $endpointSecret = config('payment.stripe.webhook_secret');

            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );

            // Procesar diferentes tipos de eventos
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    return $this->handlePaymentIntentSucceeded($event->data->object);

                case 'payment_intent.payment_failed':
                    return $this->handlePaymentIntentFailed($event->data->object);

                case 'charge.refunded':
                    return $this->handleChargeRefunded($event->data->object);

                default:
                    return [
                        'success' => true,
                        'action' => 'ignored',
                        'message' => 'Evento no procesado: ' . $event->type,
                    ];
            }
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook invalid payload', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Invalid payload',
            ];
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe webhook invalid signature', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Invalid signature',
            ];
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing error', [
                'error' => $e->getMessage(),
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
        return 'Tarjeta de Crédito (Stripe)';
    }

    /**
     * الحصول على خيارات الدفع المتاحة في هذه البوابة
     *
     * @return array خيارات الدفع المتاحة
     */
    public function getPaymentOptions(): array
    {
        return [
            'card' => [
                'name' => 'Tarjeta de Crédito/Débito',
                'icon' => 'credit-card',
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
        return true;
    }

    /**
     * الحصول على خطط الأقساط المتاحة
     *
     * @param float $amount المبلغ المراد تقسيطه
     * @return array خطط الأقساط المتاحة
     */
    public function getInstallmentPlans(float $amount): array
    {
        // Planes de cuotas disponibles (simulados)
        return [
            [
                'id' => 'installment_3',
                'name' => '3 cuotas sin interés',
                'months' => 3,
                'interest_rate' => 0,
                'monthly_payment' => round($amount / 3, 2),
                'total_amount' => $amount,
            ],
            [
                'id' => 'installment_6',
                'name' => '6 cuotas',
                'months' => 6,
                'interest_rate' => 5,
                'monthly_payment' => round(($amount * 1.05) / 6, 2),
                'total_amount' => round($amount * 1.05, 2),
            ],
            [
                'id' => 'installment_12',
                'name' => '12 cuotas',
                'months' => 12,
                'interest_rate' => 10,
                'monthly_payment' => round(($amount * 1.10) / 12, 2),
                'total_amount' => round($amount * 1.10, 2),
            ],
        ];
    }

    /**
     * Manejar evento de PaymentIntent exitoso
     *
     * @param object $paymentIntent
     * @return array
     */
    protected function handlePaymentIntentSucceeded($paymentIntent): array
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if (!$orderId) {
            return [
                'success' => false,
                'error' => 'No se encontró el ID de orden en los metadatos',
            ];
        }

        // Buscar el pago asociado
        $payment = Payment::where('transaction_id', $paymentIntent->id)->first();

        if ($payment) {
            // Confirmar el pago
            $this->confirmPayment($payment);

            // Actualizar el estado del pedido
            $order = $payment->order;
            $order->status = 'processing';
            $order->payment_status = 'completed';
            $order->save();

            return [
                'success' => true,
                'action' => 'payment_confirmed',
                'transaction_id' => $paymentIntent->id,
                'order_id' => $orderId,
            ];
        }

        return [
            'success' => false,
            'error' => 'No se encontró el registro de pago',
        ];
    }

    /**
     * Manejar evento de PaymentIntent fallido
     *
     * @param object $paymentIntent
     * @return array
     */
    protected function handlePaymentIntentFailed($paymentIntent): array
    {
        $payment = Payment::where('transaction_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->status = 'failed';
            $payment->payment_details = array_merge($payment->payment_details ?? [], [
                'error_code' => $paymentIntent->last_payment_error->code ?? null,
                'error_message' => $paymentIntent->last_payment_error->message ?? 'Unknown error',
            ]);
            $payment->save();

            return [
                'success' => true,
                'action' => 'payment_failed',
                'transaction_id' => $paymentIntent->id,
            ];
        }

        return [
            'success' => false,
            'error' => 'No se encontró el registro de pago',
        ];
    }

    /**
     * Manejar evento de cargo reembolsado
     *
     * @param object $charge
     * @return array
     */
    protected function handleChargeRefunded($charge): array
    {
        // Buscar el pago por el ID de cargo
        $payment = Payment::whereJsonContains('payment_details->charge_id', $charge->id)->first();

        if (!$payment) {
            // Intentar buscar por PaymentIntent
            $payment = Payment::whereHas('order', function ($query) use ($charge) {
                $query->where('id', $charge->metadata->order_id ?? 0);
            })->first();
        }

        if ($payment) {
            $isFullRefund = $charge->amount_refunded === $charge->amount;
            $payment->status = $isFullRefund ? 'refunded' : 'partially_refunded';
            $payment->payment_details = array_merge($payment->payment_details ?? [], [
                'refund_id' => $charge->refunds->data[0]->id ?? null,
                'refund_amount' => $this->convertFromCents($charge->amount_refunded),
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

    /**
     * Convertir monto a centavos para Stripe
     *
     * @param float $amount
     * @return int
     */
    protected function convertToCents(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Convertir monto de centavos a unidades
     *
     * @param int $amount
     * @return float
     */
    protected function convertFromCents(int $amount): float
    {
        return round($amount / 100, 2);
    }
}
