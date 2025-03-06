<?php

namespace App\Services\AI\Contracts;

interface RecommendationServiceInterface
{
    /**
     * الحصول على توصيات المنتجات المخصصة للمستخدم
     *
     * @param int $userId معرف المستخدم
     * @param int $limit عدد النتائج المطلوبة
     * @return array مصفوفة معرفات المنتجات الموصى بها
     */
    public function getPersonalizedRecommendations(int $userId, int $limit = 10): array;

    /**
     * الحصول على المنتجات المشابهة بناءً على منتج معين
     *
     * @param int $productId معرف المنتج
     * @param int $limit عدد النتائج المطلوبة
     * @return array مصفوفة معرفات المنتجات المشابهة
     */
    public function getSimilarProducts(int $productId, int $limit = 10): array;

    /**
     * الحصول على المنتجات التكميلية التي تناسب منتج معين
     *
     * @param int $productId معرف المنتج
     * @param int $limit عدد النتائج المطلوبة
     * @return array مصفوفة معرفات المنتجات التكميلية
     */
    public function getComplementaryProducts(int $productId, int $limit = 5): array;

    /**
     * الحصول على توصيات منتجات مخصصة حسب السياق (الوقت، الموقع، المناسبة)
     *
     * @param int $userId معرف المستخدم
     * @param array $context معلومات السياق (الوقت، الموقع، المناسبة...)
     * @param int $limit عدد النتائج المطلوبة
     * @return array مصفوفة معرفات المنتجات الموصى بها
     */
    public function getContextualRecommendations(int $userId, array $context, int $limit = 10): array;

    /**
     * توليد توصيات للمنتجات التي قد تهم المستخدمين المتشابهين
     *
     * @param int $userId معرف المستخدم
     * @param int $limit عدد النتائج المطلوبة
     * @return array مصفوفة معرفات المنتجات الموصى بها بناءً على المستخدمين المتشابهين
     */
    public function getCollaborativeRecommendations(int $userId, int $limit = 10): array;

    /**
     * تسجيل تفاعل المستخدم لتحسين التوصيات المستقبلية
     *
     * @param int $userId معرف المستخدم
     * @param int $productId معرف المنتج
     * @param string $interactionType نوع التفاعل (view، add_to_cart، purchase...)
     * @param array $metadata بيانات إضافية عن التفاعل
     * @return bool هل تم تسجيل التفاعل بنجاح
     */
    public function recordUserInteraction(int $userId, int $productId, string $interactionType, array $metadata = []): bool;

    /**
     * تدريب أو تحديث نموذج التوصيات
     *
     * @param string $modelType نوع النموذج المراد تدريبه
     * @param array $parameters معلمات التدريب
     * @return bool هل تم التدريب بنجاح
     */
    public function trainRecommendationModel(string $modelType, array $parameters = []): bool;
    
    /**
     * الحصول على اقتراحات قائمة على الاتجاهات والمنتجات الشائعة
     *
     * @param array $filters فلاتر لتخصيص النتائج (الفئة، السعر...)
     * @param int $limit عدد النتائج المطلوبة
     * @return array مصفوفة معرفات المنتجات الشائعة
     */
    public function getTrendingRecommendations(array $filters = [], int $limit = 10): array;
}
