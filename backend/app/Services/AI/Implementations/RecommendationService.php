<?php

namespace App\Services\AI\Implementations;

use App\Models\Ecommerce\Product;
use App\Models\User;
use App\Services\AI\Contracts\RecommendationServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecommendationService implements RecommendationServiceInterface
{
    /**
     * الحصول على توصيات المنتجات المخصصة للمستخدم
     *
     * @param int $userId معرف المستخدم
     * @param int $limit عدد النتائج المطلوبة
     * @return array مصفوفة معرفات المنتجات الموصى بها
     */
    public function getPersonalizedRecommendations(int $userId, int $limit = 10): array
    {
        $cacheKey = "user_recommendations_{$userId}_{$limit}";
        
        return Cache::remember($cacheKey, now()->addHours(1), function () use ($userId, $limit) {
            try {
                // Get user's purchase history
                $purchasedProductIds = DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.user_id', $userId)
                    ->where('orders.status', 'completed')
                    ->pluck('order_items.product_id')
                    ->toArray();
                
                // Get user's viewed products
                $viewedProductIds = DB::table('user_product_interactions')
                    ->where('user_id', $userId)
                    ->where('interaction_type', 'view')
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->pluck('product_id')
                    ->toArray();
                
                // Get categories of interest
                $categoriesOfInterest = DB::table('products')
                    ->whereIn('id', array_merge($purchasedProductIds, $viewedProductIds))
                    ->pluck('category_id')
                    ->unique()
                    ->toArray();
                
                // Get recommended products from those categories, excluding ones already purchased/viewed
                $excludeIds = array_merge($purchasedProductIds, $viewedProductIds);
                $recommendedProducts = DB::table('products')
                    ->whereIn('category_id', $categoriesOfInterest)
                    ->whereNotIn('id', $excludeIds)
                    ->where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->orderBy('average_rating', 'desc')
                    ->limit($limit)
                    ->pluck('id')
                    ->toArray();
                
                // If we don't have enough recommendations, add trending products
                if (count($recommendedProducts) < $limit) {
                    $trendingProducts = $this->getTrendingRecommendations([], $limit - count($recommendedProducts));
                    $recommendedProducts = array_merge($recommendedProducts, array_diff($trendingProducts, $recommendedProducts));
                }
                
                return array_slice($recommendedProducts, 0, $limit);
            } catch (\Exception $e) {
                Log::error('Error generating personalized recommendations', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Fallback to trending products if there's an error
                return $this->getTrendingRecommendations([], $limit);
            }
        });
    }

    /**
     * الحصول على المنتجات المشابهة بناءً على منتج معين
     *
     * @param int $productId معرف المنتج
     * @param int $limit عدد النتائج المطلوبة
     * @return array مصفوفة معرفات المنتجات المشابهة
     */
    public function getSimilarProducts(int $productId, int $limit = 10): array
    {
        $cacheKey = "similar_products_{$productId}_{$limit}";
        
        return Cache::remember($cacheKey, now()->addDay(), function () use ($productId, $limit) {
            try {
                $product = Product::findOrFail($productId);
                
                // Get products in the same category
                $similarProducts = Product::where('category_id', $product->category_id)
                    ->where('id', '!=', $productId)
                    ->where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->limit($limit * 2)
                    ->get();
                
                // Sort by price similarity
                $productPrice = $product->price;
                $similarProducts = $similarProducts->sortBy(function ($item) use ($productPrice) {
                    return abs($item->price - $productPrice);
                });
                
                return $similarProducts->take($limit)->pluck('id')->toArray();
            } catch (\Exception $e) {
                Log::error('Error finding similar products', [
                    'product_id' => $productId,
                    'error' => $e->getMessage()
                ]);
                
                return [];
            }
        });
    }

    /**
     * الحصول على المنتجات التكميلية التي تناسب منتج معين
     *
     * @param int $productId معرف المنتج
     * @param int $limit عدد النتائج المطلوبة
     * @return array مصفوفة معرفات المنتجات التكميلية
     */
    public function getComplementaryProducts(int $productId, int $limit = 5): array
    {
        $cacheKey = "complementary_products_{$productId}_{$limit}";
        
        return Cache::remember($cacheKey, now()->addDay(), function () use ($productId, $limit) {
            try {
                // Find product relationships - typically these would be defined by merchants
                // or derived from purchase patterns
                $relatedProducts = DB::table('product_relationships')
                    ->where('product_id', $productId)
                    ->where('relationship_type', 'complementary')
                    ->pluck('related_product_id')
                    ->toArray();
                
                // If we have explicit relationships, use those first
                if (count($relatedProducts) > 0) {
                    return array_slice($relatedProducts, 0, $limit);
                }
                
                // Otherwise, find products frequently bought together
                $complementaryProducts = DB::table('order_items as oi1')
                    ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
                    ->where('oi1.product_id', $productId)
                    ->where('oi2.product_id', '!=', $productId)
                    ->select('oi2.product_id', DB::raw('COUNT(*) as frequency'))
                    ->groupBy('oi2.product_id')
                    ->orderBy('frequency', 'desc')
                    ->limit($limit)
                    ->pluck('oi2.product_id')
                    ->toArray();
                
                return $complementaryProducts;
            } catch (\Exception $e) {
                Log::error('Error finding complementary products', [
                    'product_id' => $productId,
                    'error' => $e->getMessage()
                ]);
                
                return [];
            }
        });
    }

    /**
     * الحصول على توصيات منتجات مخصصة حسب السياق (الوقت، الموقع، المناسبة)
     *
     * @param int $userId معرف المستخدم
     * @param array $context معلومات السياق (الوقت، الموقع، المناسبة...)
     * @param int $limit عدد النتائج المطلوبة
     * @return array مصفوفة معرفات المنتجات الموصى بها
     */
    public function getContextualRecommendations(int $userId, array $context, int $limit = 10): array
    {
        try {
            $recommendations = [];
            
            // Handle time-based context (e.g., seasonal products)
            if (isset($context['season'])) {
                $seasonalProducts = DB::table('products')
                    ->join('product_tags', 'products.id', '=', 'product_tags.product_id')
                    ->join('tags', 'product_tags.tag_id', '=', 'tags.id')
                    ->where('tags.name', 'like', "%{$context['season']}%")
                    ->where('products.is_active', true)
                    ->where('products.stock_quantity', '>', 0)
                    ->pluck('products.id')
                    ->toArray();
                
                $recommendations = array_merge($recommendations, $seasonalProducts);
            }
            
            // Handle location-based context
            if (isset($context['location'])) {
                $locationProducts = DB::table('products')
                    ->join('merchants', 'products.merchant_id', '=', 'merchants.id')
                    ->where('merchants.city', $context['location'])
                    ->where('products.is_active', true)
                    ->where('products.stock_quantity', '>', 0)
                    ->limit($limit)
                    ->pluck('products.id')
                    ->toArray();
                
                $recommendations = array_merge($recommendations, $locationProducts);
            }
            
            // Handle event-based context
            if (isset($context['event'])) {
                $eventProducts = DB::table('products')
                    ->join('product_tags', 'products.id', '=', 'product_tags.product_id')
                    ->join('tags', 'product_tags.tag_id', '=', 'tags.id')
                    ->where('tags.name', 'like', "%{$context['event']}%")
                    ->where('products.is_active', true)
                    ->where('products.stock_quantity', '>', 0)
                    ->pluck('products.id')
                    ->toArray();
                
                $recommendations = array_merge($recommendations, $eventProducts);
            }
            
            // De-duplicate and limit results
            $recommendations = array_unique($recommendations);
            $recommendations = array_slice($recommendations, 0, $limit);
            
            // If we don't have enough recommendations, add personalized ones
            if (count($recommendations) < $limit) {
                $personalRecommendations = $this->getPersonalizedRecommendations($userId, $limit - count($recommendations));
                $recommendations = array_merge($recommendations, array_diff($personalRecommendations, $recommendations));
            }
            
            return array_slice($recommendations, 0, $limit);
        } catch (\Exception $e) {
            Log::error('Error generating contextual recommendations', [
                'user_id' => $userId,
                'context' => $context,
                'error' => $e->getMessage()
            ]);
            
            return $this->getPersonalizedRecommendations($userId, $limit);
        }
    }

    /**
     * توليد توصيات للمنتجات التي قد تهم المستخدمين المتشابهين
     *
     * @param int $userId معرف المستخدم
     * @param int $limit عدد النتائج المطلوبة
     * @return array مصفوفة معرفات المنتجات الموصى بها بناءً على المستخدمين المتشابهين
     */
    public function getCollaborativeRecommendations(int $userId, int $limit = 10): array
    {
        $cacheKey = "collaborative_recommendations_{$userId}_{$limit}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($userId, $limit) {
            try {
                // Get user's purchase history
                $userPurchases = DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.user_id', $userId)
                    ->where('orders.status', 'completed')
                    ->pluck('order_items.product_id')
                    ->toArray();
                
                // Find users with similar purchase patterns
                $similarUsers = DB::table('orders')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->whereIn('order_items.product_id', $userPurchases)
                    ->where('orders.user_id', '!=', $userId)
                    ->where('orders.status', 'completed')
                    ->select('orders.user_id', DB::raw('COUNT(DISTINCT order_items.product_id) as matches'))
                    ->groupBy('orders.user_id')
                    ->orderBy('matches', 'desc')
                    ->limit(20)
                    ->pluck('orders.user_id')
                    ->toArray();
                
                if (empty($similarUsers)) {
                    return $this->getTrendingRecommendations([], $limit);
                }
                
                // Find products purchased by similar users but not by this user
                $recommendedProducts = DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->whereIn('orders.user_id', $similarUsers)
                    ->whereNotIn('order_items.product_id', $userPurchases)
                    ->where('orders.status', 'completed')
                    ->select('order_items.product_id', DB::raw('COUNT(DISTINCT orders.user_id) as frequency'))
                    ->groupBy('order_items.product_id')
                    ->orderBy('frequency', 'desc')
                    ->limit($limit)
                    ->pluck('order_items.product_id')
                    ->toArray();
                
                return $recommendedProducts;
            } catch (\Exception $e) {
                Log::error('Error generating collaborative recommendations', [
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
                
                return $this->getTrendingRecommendations([], $limit);
            }
        });
    }

    /**
     * تسجيل تفاعل المستخدم لتحسين التوصيات المستقبلية
     *
     * @param int $userId معرف المستخدم
     * @param int $productId معرف المنتج
     * @param string $interactionType نوع التفاعل (view، add_to_cart، purchase...)
     * @param array $metadata بيانات إضافية عن التفاعل
     * @return bool هل تم تسجيل التفاعل بنجاح
     */
    public function recordUserInteraction(int $userId, int $productId, string $interactionType, array $metadata = []): bool
    {
        try {
            // Clear the recommendation cache for this user
            Cache::forget("user_recommendations_{$userId}_10");
            
            return DB::table('user_product_interactions')->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'interaction_type' => $interactionType,
                'metadata' => json_encode($metadata),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error recording user interaction', [
                'user_id' => $userId,
                'product_id' => $productId,
                'interaction_type' => $interactionType,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * تدريب أو تحديث نموذج التوصيات
     *
     * @param string $modelType نوع النموذج المراد تدريبه
     * @param array $parameters معلمات التدريب
     * @return bool هل تم التدريب بنجاح
     */
    public function trainRecommendationModel(string $modelType, array $parameters = []): bool
    {
        // This would typically connect to an external machine learning service or run
        // local training algorithms. For now, we'll just log the request and return true.
        Log::info('Training recommendation model requested', [
            'model_type' => $modelType,
            'parameters' => $parameters
        ]);
        
        // Clear all recommendation caches to ensure fresh data after training
        Cache::tags(['recommendations'])->flush();
        
        return true;
    }
    
    /**
     * الحصول على اقتراحات قائمة على الاتجاهات والمنتجات الشائعة
     *
     * @param array $filters فلاتر لتخصيص النتائج (الفئة، السعر...)
     * @param int $limit عدد النتائج المطلوبة
     * @return array مصفوفة معرفات المنتجات الشائعة
     */
    public function getTrendingRecommendations(array $filters = [], int $limit = 10): array
    {
        $cacheKey = "trending_products_" . md5(json_encode($filters)) . "_{$limit}";
        
        return Cache::remember($cacheKey, now()->addHours(3), function () use ($filters, $limit) {
            try {
                // Start with a base query for products
                $query = DB::table('products')
                    ->where('is_active', true)
                    ->where('stock_quantity', '>', 0);
                
                // Apply category filter if specified
                if (isset($filters['category_id'])) {
                    $query->where('category_id', $filters['category_id']);
                }
                
                // Apply price range filter if specified
                if (isset($filters['min_price'])) {
                    $query->where('price', '>=', $filters['min_price']);
                }
                
                if (isset($filters['max_price'])) {
                    $query->where('price', '<=', $filters['max_price']);
                }
                
                // Get trending products based on recent views, cart additions, and purchases
                $trendingProducts = $query
                    ->leftJoin('user_product_interactions', 'products.id', '=', 'user_product_interactions.product_id')
                    ->select('products.id', DB::raw('COUNT(user_product_interactions.id) as interaction_count'))
                    ->where(function ($q) {
                        $q->whereNull('user_product_interactions.created_at')
                            ->orWhere('user_product_interactions.created_at', '>=', now()->subDays(7));
                    })
                    ->groupBy('products.id')
                    ->orderBy('interaction_count', 'desc')
                    ->orderBy('products.average_rating', 'desc')
                    ->limit($limit)
                    ->pluck('products.id')
                    ->toArray();
                
                return $trendingProducts;
            } catch (\Exception $e) {
                Log::error('Error finding trending products', [
                    'filters' => $filters,
                    'error' => $e->getMessage()
                ]);
                
                // Fallback to newest products
                return DB::table('products')
                    ->where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->pluck('id')
                    ->toArray();
            }
        });
    }
}
