<?php

namespace App\Services\Payment\Contracts;

use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\Payment;

interface PaymentGatewayInterface
{
    /**
     * إنشاء معاملة دفع جديدة
     *
     * @param Order $order
     * @param array $paymentData
     * @return array بيانات معاملة الدفع بما في ذلك رابط الدفع أو رمز المعاملة
     */
    public function createTransaction(Order $order, array $paymentData = []): array;
    
    /**
     * التحقق من حالة معاملة دفع
     *
     * @param string $transactionId
     * @return array معلومات حالة معاملة الدفع
     */
    public function checkTransactionStatus(string $transactionId): array;
    
    /**
     * تأكيد عملية الدفع بعد اكتمالها
     *
     * @param Payment $payment
     * @param array $data
     * @return bool نجاح أو فشل تأكيد عملية الدفع
     */
    public function confirmPayment(Payment $payment, array $data = []): bool;
    
    /**
     * إلغاء معاملة دفع
     *
     * @param Payment $payment
     * @return bool نجاح أو فشل إلغاء معاملة الدفع
     */
    public function cancelTransaction(Payment $payment): bool;
    
    /**
     * طلب استرداد مبلغ عملية دفع
     *
     * @param Payment $payment
     * @param float|null $amount المبلغ المراد استرداده، إذا كان null فسيتم استرداد كامل المبلغ
     * @param string $reason سبب الاسترداد
     * @return array معلومات عملية الاسترداد
     */
    public function refundTransaction(Payment $payment, ?float $amount = null, string $reason = ''): array;
    
    /**
     * معالجة إشعار الويب هوك من بوابة الدفع
     *
     * @param array $data بيانات الويب هوك
     * @return array معلومات حول معالجة الويب هوك
     */
    public function handleWebhook(array $data): array;
    
    /**
     * الحصول على اسم بوابة الدفع
     *
     * @return string اسم بوابة الدفع
     */
    public function getName(): string;
    
    /**
     * الحصول على خيارات الدفع المتاحة في هذه البوابة
     *
     * @return array خيارات الدفع المتاحة
     */
    public function getPaymentOptions(): array;
    
    /**
     * التحقق مما إذا كانت البوابة تدعم الدفع بالأقساط
     *
     * @return bool
     */
    public function supportsInstallments(): bool;
    
    /**
     * الحصول على خطط الأقساط المتاحة
     *
     * @param float $amount المبلغ المراد تقسيطه
     * @return array خطط الأقساط المتاحة
     */
    public function getInstallmentPlans(float $amount): array;
}
