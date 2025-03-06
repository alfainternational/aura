<?php

namespace App\Services\AI\Contracts;

interface ProductAnalysisServiceInterface
{
    /**
     * تحليل بيانات المنتج واقتراح تحسينات
     * 
     * @param array $productData بيانات المنتج
     * @return array اقتراحات التحسين
     */
    public function analyzeProduct(array $productData): array;
    
    /**
     * تحسين وصف المنتج
     * 
     * @param string $description الوصف الحالي
     * @param array $productAttributes سمات المنتج
     * @return string الوصف المحسن
     */
    public function enhanceProductDescription(string $description, array $productAttributes): string;
    
    /**
     * اقتراح كلمات مفتاحية للمنتج
     * 
     * @param array $productData بيانات المنتج
     * @return array الكلمات المفتاحية المقترحة
     */
    public function suggestKeywords(array $productData): array;
    
    /**
     * تقييم جودة صور المنتج
     * 
     * @param array $imageUrls مسارات الصور
     * @return array تقييم جودة الصور
     */
    public function evaluateProductImages(array $imageUrls): array;
}
