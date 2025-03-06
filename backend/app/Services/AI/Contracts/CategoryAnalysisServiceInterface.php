<?php

namespace App\Services\AI\Contracts;

interface CategoryAnalysisServiceInterface
{
    /**
     * تحليل المنتج وإقتراح الفئات المناسبة له
     *
     * @param array $productData بيانات المنتج (الاسم، الوصف، المواصفات)
     * @param int $limit عدد الفئات المقترحة
     * @return array مصفوفة تحتوي على الفئات المقترحة مع درجة الثقة
     */
    public function suggestCategories(array $productData, int $limit = 5): array;

    /**
     * إنشاء فئات جديدة بناءً على تحليل مجموعة من المنتجات
     *
     * @param array $productsData مصفوفة من بيانات المنتجات
     * @param int $maxCategories الحد الأقصى لعدد الفئات المقترحة
     * @return array مصفوفة تحتوي على الفئات المقترحة مع الخصائص
     */
    public function generateCategorySuggestions(array $productsData, int $maxCategories = 10): array;

    /**
     * تحليل خصائص الفئة وإقتراح سمات مميزة لها
     *
     * @param int $categoryId معرف الفئة
     * @return array مصفوفة تحتوي على السمات المميزة للفئة
     */
    public function analyzeCategoryAttributes(int $categoryId): array;

    /**
     * تحسين وصف الفئة بناءً على المنتجات المنتمية إليها
     *
     * @param int $categoryId معرف الفئة
     * @return string الوصف المحسن للفئة
     */
    public function enhanceCategoryDescription(int $categoryId): string;

    /**
     * إعادة تنظيم المنتجات في الفئات بناءً على التحليل الذكي
     *
     * @param int $categoryId معرف الفئة الأم (اختياري)
     * @return array مصفوفة تحتوي على اقتراحات إعادة التنظيم
     */
    public function suggestCategoryReorganization(?int $categoryId = null): array;

    /**
     * اكتشاف الفئات المتشابهة التي يمكن دمجها
     *
     * @return array مصفوفة تحتوي على أزواج من الفئات المتشابهة
     */
    public function detectSimilarCategories(): array;

    /**
     * توليد كلمات مفتاحية ذكية للفئة لتحسين البحث
     *
     * @param int $categoryId معرف الفئة
     * @return array مصفوفة تحتوي على الكلمات المفتاحية المقترحة
     */
    public function generateCategoryKeywords(int $categoryId): array;
}
