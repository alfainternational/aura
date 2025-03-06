<?php

namespace App\Services\Payment;

use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\Payment;
use App\Models\User;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuraWalletGateway implements PaymentGatewayInterface
{
    /**
     * @var WalletService
     */
    protected $walletService;

    /**
     * Constructor
     *
     * @param WalletService $walletService
     */
    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
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
            // Verificar que el usuario existe y tiene una billetera
            $user = User::find($order->user_id);
            
            if (!$user) {
                return [
                    'success' => false,
                    'error' => 'Usuario no encontrado',
                ];
            }

            // Verificar si el usuario tiene saldo suficiente
            $walletBalance = $this->walletService->getBalance($user);
            
            if (!$this->walletService->hasSufficientBalance($user, $order->total)) {
                return [
                    'success' => false,
                    'error' => 'Saldo insuficiente en la billetera. Saldo actual: ' . number_format($walletBalance, 2) . ' ' . $order->currency,
                    'current_balance' => $walletBalance,
                    'required_amount' => $order->total,
                ];
            }

            // Generar ID de transacción único
            $transactionId = 'WALLET-' . uniqid() . '-' . $order->id;

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => 'pending',
                'amount' => $order->total,
                'currency' => $order->currency ?? 'USD',
                'current_balance' => $walletBalance,
                'remaining_balance' => $walletBalance - $order->total,
            ];
        } catch (\Exception $e) {
            Log::error('Aura Wallet payment creation failed', [
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
        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            return [
                'success' => false,
                'status' => 'error',
                'error' => 'Transacción no encontrada',
            ];
        }

        // Para la billetera, el estado se actualiza inmediatamente después de la confirmación
        return [
            'success' => true,
            'status' => $payment->status,
            'transaction_id' => $transactionId,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
        ];
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
            // Iniciar transacción para asegurar la integridad de los datos
            return DB::transaction(function () use ($payment) {
                $order = $payment->order;
                $user = User::find($order->user_id);

                if (!$user) {
                    throw new \Exception('Usuario no encontrado');
                }

                // Procesar el pago con el servicio de billetera
                $description = "Pago del pedido #{$order->order_number}";
                $details = [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'payment_id' => $payment->id,
                ];
                
                $transaction = $this->walletService->processPayment(
                    $user,
                    $payment->amount,
                    $description,
                    $details,
                    $payment->transaction_id,
                    'order_payment'
                );

                // Actualizar el estado del pago
                $payment->status = 'completed';
                $payment->payment_details = array_merge($payment->payment_details ?? [], [
                    'wallet_transaction_id' => $transaction->id,
                    'previous_balance' => $user->wallet_balance + $payment->amount,
                    'new_balance' => $user->wallet_balance,
                ]);
                $payment->save();

                // Actualizar el estado del pedido
                $order->status = 'processing';
                $order->payment_status = 'completed';
                $order->save();

                return true;
            });
        } catch (\Exception $e) {
            Log::error('Aura Wallet payment confirmation failed', [
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
        $payment->status = 'canceled';
        return $payment->save();
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
            // Iniciar transacción para asegurar la integridad de los datos
            return DB::transaction(function () use ($payment, $amount, $reason) {
                $refundAmount = $amount ?? $payment->amount;
                $user = User::find($payment->order->user_id);

                if (!$user) {
                    throw new \Exception('Usuario no encontrado');
                }

                // Generar ID de reembolso único
                $refundId = 'WALLET-REFUND-' . uniqid();

                // Procesar el reembolso con el servicio de billetera
                $description = "Reembolso del pedido #{$payment->order->order_number}: " . $reason;
                $details = [
                    'order_id' => $payment->order->id,
                    'order_number' => $payment->order->order_number,
                    'payment_id' => $payment->id,
                    'refund_reason' => $reason,
                    'refund_id' => $refundId,
                ];
                
                $transaction = $this->walletService->processRefund(
                    $user,
                    $refundAmount,
                    $description,
                    $details,
                    $refundId,
                    'order_refund'
                );

                // Actualizar el estado del pago
                $payment->status = $amount === null ? 'refunded' : 'partially_refunded';
                $payment->payment_details = array_merge($payment->payment_details ?? [], [
                    'refund_id' => $refundId,
                    'refund_amount' => $refundAmount,
                    'refund_reason' => $reason,
                    'refund_date' => now()->toIso8601String(),
                    'wallet_refund_transaction_id' => $transaction->id,
                    'previous_balance' => $user->wallet_balance - $refundAmount,
                    'new_balance' => $user->wallet_balance,
                ]);
                $payment->save();

                return [
                    'success' => true,
                    'refund_id' => $refundId,
                    'amount' => $refundAmount,
                    'currency' => $payment->currency,
                    'status' => 'completed',
                    'wallet_transaction_id' => $transaction->id,
                    'new_balance' => $user->wallet_balance,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Aura Wallet refund failed', [
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
        // La billetera no utiliza webhooks
        return [
            'success' => true,
            'action' => 'ignored',
            'message' => 'La billetera Aura no utiliza webhooks',
        ];
    }

    /**
     * الحصول على اسم بوابة الدفع
     *
     * @return string اسم بوابة الدفع
     */
    public function getName(): string
    {
        return 'Billetera Aura';
    }

    /**
     * الحصول على خيارات الدفع المتاحة في هذه البوابة
     *
     * @return array خيارات الدفع المتاحة
     */
    public function getPaymentOptions(): array
    {
        return [
            'wallet' => [
                'name' => 'Billetera Aura',
                'icon' => 'wallet',
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
}
