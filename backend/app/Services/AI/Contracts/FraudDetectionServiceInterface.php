<?php

namespace App\Services\AI\Contracts;

interface FraudDetectionServiceInterface
{
    /**
     * تحليل معاملة للكشف عن احتمالية الاحتيال
     * 
     * @param array $transactionData بيانات المعاملة
     * @return array نتيجة التحليل مع درجة المخاطرة
     */
    public function analyzeTransaction(array $transactionData): array;
    
    /**
     * تحليل سلوك المستخدم للكشف عن أنماط مشبوهة
     * 
     * @param int $userId معرف المستخدم
     * @param array $activityData بيانات النشاط
     * @return array تقرير تحليل السلوك
     */
    public function analyzeUserBehavior(int $userId, array $activityData): array;
    
    /**
     * التحقق من صحة معلومات الدفع
     * 
     * @param array $paymentInfo معلومات الدفع
     * @return bool هل المعلومات صحيحة
     */
    public function validatePaymentInformation(array $paymentInfo): bool;
    
    /**
     * الحصول على قائمة بالمعاملات المشبوهة
     * 
     * @param array $filters مرشحات البحث
     * @return array قائمة المعاملات المشبوهة
     */
    public function getSuspiciousTransactions(array $filters = []): array;
}
