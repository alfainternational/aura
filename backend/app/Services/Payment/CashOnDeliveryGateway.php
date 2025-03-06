<?php

namespace App\Services\Payment;

use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

class CashOnDeliveryGateway implements PaymentGatewayInterface
{
    /**
     * إنشاء معاملة دفع جديدة
     *
     * @param Order $order
     * @param array $paymentData
     * @return array بيانات معاملة الدفع بما في ذلك رابط الدفع أو رمز المعاملة
     */
    public function createTransaction(Order $order, array $paymentData = []): array
    {
        // Verificar si el monto está dentro de los límites permitidos para COD
        $minAmount = config('payment.cod.minimum_order_value', 0);
        $maxAmount = config('payment.cod.maximum_order_value', 5000);

        if ($order->total < $minAmount) {
            return [
                'success' => false,
                'error' => "El monto mínimo para pago contra entrega es " . number_format($minAmount, 2) . " " . $order->currency,
            ];
        }

        if ($order->total > $maxAmount) {
            return [
                'success' => false,
                'error' => "El monto máximo para pago contra entrega es " . number_format($maxAmount, 2) . " " . $order->currency,
            ];
        }

        // Para COD, simplemente generamos un ID de transacción único
        $transactionId = 'COD-' . uniqid() . '-' . $order->id;

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'status' => 'pending',
            'amount' => $order->total,
            'currency' => $order->currency ?? 'USD',
            'message' => 'Pedido registrado correctamente. Pago pendiente contra entrega.',
        ];
    }

    /**
     * التحقق من حالة معاملة دفع
     *
     * @param string $transactionId
     * @return array معلومات حالة معاملة الدفع
     */
    public function checkTransactionStatus(string $transactionId): array
    {
        // Para COD, el estado siempre es pendiente hasta que se confirme manualmente
        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            return [
                'success' => false,
                'status' => 'error',
                'error' => 'Transacción no encontrada',
            ];
        }

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
        // Confirmar el pago manualmente (generalmente lo hace un administrador)
        $payment->status = 'completed';
        $payment->payment_details = array_merge($payment->payment_details ?? [], [
            'confirmed_by' => $data['user_id'] ?? auth()->id(),
            'confirmation_date' => now()->toIso8601String(),
            'confirmation_notes' => $data['notes'] ?? 'Pago confirmado manualmente',
        ]);
        
        return $payment->save();
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
        // Para COD, el reembolso es manual y simplemente actualizamos el estado
        $refundId = 'COD-REFUND-' . uniqid();
        $refundAmount = $amount ?? $payment->amount;

        $payment->status = $amount === null ? 'refunded' : 'partially_refunded';
        $payment->payment_details = array_merge($payment->payment_details ?? [], [
            'refund_id' => $refundId,
            'refund_amount' => $refundAmount,
            'refund_reason' => $reason,
            'refund_date' => now()->toIso8601String(),
        ]);
        $payment->save();

        return [
            'success' => true,
            'refund_id' => $refundId,
            'amount' => $refundAmount,
            'currency' => $payment->currency,
            'status' => 'completed',
            'message' => 'Reembolso registrado correctamente',
        ];
    }

    /**
     * معالجة إشعار الويب هوك من بوابة الدفع
     *
     * @param array $data بيانات الويب هوك
     * @return array معلومات حول معالجة الويب هوك
     */
    public function handleWebhook(array $data): array
    {
        // COD no utiliza webhooks
        return [
            'success' => true,
            'action' => 'ignored',
            'message' => 'El método de pago contra entrega no utiliza webhooks',
        ];
    }

    /**
     * الحصول على اسم بوابة الدفع
     *
     * @return string اسم بوابة الدفع
     */
    public function getName(): string
    {
        return 'Pago contra entrega';
    }

    /**
     * الحصول على خيارات الدفع المتاحة في هذه البوابة
     *
     * @return array خيارات الدفع المتاحة
     */
    public function getPaymentOptions(): array
    {
        return [
            'cod' => [
                'name' => 'Pago contra entrega',
                'icon' => 'money-bill',
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
