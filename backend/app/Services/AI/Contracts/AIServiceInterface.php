<?php

namespace App\Services\AI\Contracts;

interface AIServiceInterface
{
    /**
     * معالجة طلب ذكي باستخدام نماذج الذكاء الاصطناعي
     * 
     * @param string $input المدخلات
     * @param array $context السياق الإضافي
     * @param array $options الخيارات الإضافية
     * @return array نتائج المعالجة
     */
    public function processSmartRequest(string $input, array $context = [], array $options = []): array;

    /**
     * التحقق من صلاحية الطلب وتقييم المخاطر
     * 
     * @param mixed $input المدخلات للتحقق
     * @return array نتائج التحقق والتقييم
     */
    public function validateRequest($input): array;

    /**
     * تسجيل عملية الذكاء الاصطناعي
     * 
     * @param string $operation العملية المنفذة
     * @param array $data بيانات العملية
     * @param bool $success حالة نجاح العملية
     */
    public function logOperation(string $operation, array $data, bool $success = true): void;

    /**
     * الحصول على إحصائيات وتفاصيل أداء الخدمة
     * 
     * @return array إحصائيات الأداء
     */
    public function getPerformanceMetrics(): array;
}
