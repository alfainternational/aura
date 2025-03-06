<?php

namespace App\Services\AI\Contracts;

interface ContentModerationServiceInterface
{
    /**
     * فحص محتوى للتأكد من عدم وجود محتوى غير لائق
     * 
     * @param string $content المحتوى المراد فحصه
     * @param string $contentType نوع المحتوى (نص، صورة، فيديو)
     * @return array نتائج الفحص مع التصنيفات
     */
    public function analyzeContent(string $content, string $contentType = 'text'): array;

    /**
     * التحقق من مدى ملاءمة المحتوى للمستخدمين
     * 
     * @param string $content المحتوى المراد التحقق منه
     * @param array $userContext سياق المستخدم
     * @return bool هل المحتوى مقبول
     */
    public function isContentAppropriate(string $content, array $userContext = []): bool;

    /**
     * تصنيف المحتوى حسب فئات محددة
     * 
     * @param string $content المحتوى المراد تصنيفه
     * @return array التصنيفات والثقة
     */
    public function classifyContent(string $content): array;
}
