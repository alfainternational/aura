<?php

namespace App\Services\Payment\Gateways;

use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;

class CashOnDeliveryGateway implements PaymentGatewayInterface
{
    /**
     * {@inheritdoc}
     */
    public function createTransaction(Order $order, array $paymentData = []): array
    {
        try {
            // إنشاء معرف المعاملة
            $transactionId = 'cod-' . uniqid();
            
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'amount' => $order->total,
                'currency' => $order->currency,
                'status' => 'pending',
                'payment_method' => 'cod',
            ];
        } catch (\Exception $e) {
            Log::error('Cash on delivery payment creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => 'فشل في إنشاء معاملة الدفع: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function checkTransactionStatus(string $transactionId): array
    {
        // الدفع عند الاستلام يكون دائمًا معلقًا حتى استلام المنتج
        return [
            'success' => true,
            'status' => 'pending',
            'transaction_id' => $transactionId,
            'payment_method' => 'cod',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function confirmPayment(Payment $payment, array $data = []): bool
    {
        try {
            // في حالة الدفع عند الاستلام، نقوم بتحديث حالة الدفع فقط
            $payment->markAsCompleted($payment->transaction_id, [
                'confirmation_date' => now()->toIso8601String(),
                'payment_method' => 'cash_on_delivery',
                'status' => 'completed',
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Cash on delivery payment confirmation failed', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function cancelTransaction(Payment $payment): bool
    {
        try {
            // تحديث حالة الدفع
            $payment->status = 'cancelled';
            $payment->gateway_response = array_merge($payment->gateway_response ?? [], [
                'cancellation_date' => now()->toIso8601String(),
                'cancellation_reason' => 'تم إلغاء الدفع',
            ]);
            $payment->save();
            
            return true;
        } catch (\Exception $e) {
            Log::error('Cash on delivery payment cancellation failed', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function refundTransaction(Payment $payment, ?float $amount = null, string $reason = ''): array
    {
        // ليس هناك حاجة لعملية استرداد الكترونية في الدفع عند الاستلام
        return [
            'success' => true,
            'status' => 'manual_refund_required',
            'message' => 'الاسترداد للدفع عند الاستلام يتطلب تدخلًا يدويًا',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function handleWebhook(array $data): array
    {
        // ليس مطلوبًا للدفع عند الاستلام
        return [
            'success' => true,
            'event_type' => 'no_webhook',
            'action' => 'no_action',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'الدفع عند الاستلام';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPaymentOptions(): array
    {
        return [
            'cash' => 'دفع نقدي',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function supportsInstallments(): bool
    {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getInstallmentPlans(float $amount): array
    {
        return [];
    }
}
