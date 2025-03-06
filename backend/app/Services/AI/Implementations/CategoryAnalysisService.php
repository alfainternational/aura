<?php

namespace App\Services\AI\Implementations;

use App\Models\Ecommerce\Product;
use App\Models\Ecommerce\ProductCategory;
use App\Services\AI\Contracts\CategoryAnalysisServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryAnalysisService implements CategoryAnalysisServiceInterface
{
    /**
     * مفتاح API للخدمة الخارجية للذكاء الاصطناعي
     */
    protected $apiKey;

    /**
     * URL الخدمة الخارجية للذكاء الاصطناعي
     */
    protected $apiUrl;

    /**
     * إنشاء مثيل جديد من الخدمة
     */
    public function __construct()
    {
        $this->apiKey = config('services.ai.api_key');
        $this->apiUrl = config('services.ai.api_url');
    }

    /**
     * تحليل المنتج وإقتراح الفئات المناسبة له
     *
     * @param array $productData بيانات المنتج (الاسم، الوصف، المواصفات)
     * @param int $limit عدد الفئات المقترحة
     * @return array مصفوفة تحتوي على الفئات المقترحة مع درجة الثقة
     */
    public function suggestCategories(array $productData, int $limit = 5): array
    {
        try {
            // تحضير البيانات للتحليل
            $analysisData = [
                'name' => $productData['name'] ?? '',
                'description' => $productData['description'] ?? '',
                'specifications' => $productData['specifications'] ?? [],
                'keywords' => $productData['keywords'] ?? [],
            ];

            // استخدام الذكاء الاصطناعي لتحليل البيانات
            $response = $this->callAiService('suggest-categories', [
                'product' => $analysisData,
                'limit' => $limit
            ]);

            if (!$response || !isset($response['categories'])) {
                // استخدام طريقة احتياطية في حالة فشل خدمة الذكاء الاصطناعي
                return $this->fallbackCategorySuggestion($analysisData, $limit);
            }

            return $response['categories'];
        } catch (\Exception $e) {
            Log::error('Error suggesting categories: ' . $e->getMessage());
            return $this->fallbackCategorySuggestion($productData, $limit);
        }
    }

    /**
     * إنشاء فئات جديدة بناءً على تحليل مجموعة من المنتجات
     *
     * @param array $productsData مصفوفة من بيانات المنتجات
     * @param int $maxCategories الحد الأقصى لعدد الفئات المقترحة
     * @return array مصفوفة تحتوي على الفئات المقترحة مع الخصائص
     */
    public function generateCategorySuggestions(array $productsData, int $maxCategories = 10): array
    {
        try {
            // استخدام الذكاء الاصطناعي لتحليل مجموعة المنتجات واقتراح فئات جديدة
            $response = $this->callAiService('generate-categories', [
                'products' => $productsData,
                'max_categories' => $maxCategories
            ]);

            if (!$response || !isset($response['suggested_categories'])) {
                return [];
            }

            $suggestedCategories = $response['suggested_categories'];

            // إضافة معلومات إضافية لكل فئة مقترحة
            foreach ($suggestedCategories as &$category) {
                // إنشاء slug للفئة
                $category['slug'] = Str::slug($category['name']);
                
                // التحقق من عدم وجود تكرار في الـ slug
                $existingCategory = ProductCategory::where('slug', $category['slug'])->first();
                if ($existingCategory) {
                    $category['slug'] = $category['slug'] . '-' . Str::random(5);
                }
            }

            return $suggestedCategories;
        } catch (\Exception $e) {
            Log::error('Error generating category suggestions: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * تحليل خصائص الفئة وإقتراح سمات مميزة لها
     *
     * @param int $categoryId معرف الفئة
     * @return array مصفوفة تحتوي على السمات المميزة للفئة
     */
    public function analyzeCategoryAttributes(int $categoryId): array
    {
        try {
            $category = ProductCategory::findOrFail($categoryId);
            
            // الحصول على منتجات الفئة
            $products = Product::where('category_id', $categoryId)
                ->select('id', 'name', 'description', 'specifications')
                ->limit(100)
                ->get()
                ->toArray();
            
            if (empty($products)) {
                return [];
            }

            // استخدام الذكاء الاصطناعي لتحليل خصائص المنتجات في هذه الفئة
            $response = $this->callAiService('analyze-category-attributes', [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description
                ],
                'products' => $products
            ]);

            if (!$response || !isset($response['attributes'])) {
                return [];
            }

            return $response['attributes'];
        } catch (\Exception $e) {
            Log::error('Error analyzing category attributes: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * تحسين وصف الفئة بناءً على المنتجات المنتمية إليها
     *
     * @param int $categoryId معرف الفئة
     * @return string الوصف المحسن للفئة
     */
    public function enhanceCategoryDescription(int $categoryId): string
    {
        try {
            $category = ProductCategory::findOrFail($categoryId);
            
            // الحصول على منتجات الفئة
            $products = Product::where('category_id', $categoryId)
                ->select('name', 'description')
                ->limit(20)
                ->get()
                ->toArray();
            
            if (empty($products)) {
                return $category->description ?? '';
            }

            // استخدام الذكاء الاصطناعي لتحسين وصف الفئة
            $response = $this->callAiService('enhance-category-description', [
                'category' => [
                    'name' => $category->name,
                    'current_description' => $category->description
                ],
                'products' => $products
            ]);

            if (!$response || !isset($response['enhanced_description'])) {
                return $category->description ?? '';
            }

            return $response['enhanced_description'];
        } catch (\Exception $e) {
            Log::error('Error enhancing category description: ' . $e->getMessage());
            return $category->description ?? '';
        }
    }

    /**
     * إعادة تنظيم المنتجات في الفئات بناءً على التحليل الذكي
     *
     * @param int $categoryId معرف الفئة الأم (اختياري)
     * @return array مصفوفة تحتوي على اقتراحات إعادة التنظيم
     */
    public function suggestCategoryReorganization(?int $categoryId = null): array
    {
        try {
            // تحديد نطاق الفئات للتحليل
            $query = ProductCategory::with('products');
            
            if ($categoryId) {
                $category = ProductCategory::findOrFail($categoryId);
                $categoryIds = $category->descendants()->pluck('id')->push($category->id);
                $query->whereIn('id', $categoryIds);
            }
            
            $categories = $query->get()->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'parent_id' => $category->parent_id,
                    'product_count' => $category->products->count(),
                ];
            })->toArray();
            
            // الحصول على عينة من المنتجات في كل فئة
            $productSamples = [];
            foreach ($query->get() as $category) {
                $products = Product::where('category_id', $category->id)
                    ->select('id', 'name', 'description', 'specifications')
                    ->limit(10)
                    ->get()
                    ->toArray();
                
                if (!empty($products)) {
                    $productSamples[$category->id] = $products;
                }
            }
            
            // استخدام الذكاء الاصطناعي لاقتراح إعادة تنظيم الفئات
            $response = $this->callAiService('suggest-reorganization', [
                'categories' => $categories,
                'product_samples' => $productSamples
            ]);

            if (!$response || !isset($response['reorganization_suggestions'])) {
                return [];
            }

            return $response['reorganization_suggestions'];
        } catch (\Exception $e) {
            Log::error('Error suggesting category reorganization: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * اكتشاف الفئات المتشابهة التي يمكن دمجها
     *
     * @return array مصفوفة تحتوي على أزواج من الفئات المتشابهة
     */
    public function detectSimilarCategories(): array
    {
        try {
            // الحصول على جميع الفئات مع أوصافها
            $categories = ProductCategory::select('id', 'name', 'description', 'parent_id')
                ->get()
                ->toArray();
            
            // استخدام الذكاء الاصطناعي لاكتشاف الفئات المتشابهة
            $response = $this->callAiService('detect-similar-categories', [
                'categories' => $categories
            ]);

            if (!$response || !isset($response['similar_pairs'])) {
                return [];
            }

            return $response['similar_pairs'];
        } catch (\Exception $e) {
            Log::error('Error detecting similar categories: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * توليد كلمات مفتاحية ذكية للفئة لتحسين البحث
     *
     * @param int $categoryId معرف الفئة
     * @return array مصفوفة تحتوي على الكلمات المفتاحية المقترحة
     */
    public function generateCategoryKeywords(int $categoryId): array
    {
        try {
            $category = ProductCategory::findOrFail($categoryId);
            
            // الحصول على منتجات الفئة
            $products = Product::where('category_id', $categoryId)
                ->select('name', 'description', 'specifications')
                ->limit(50)
                ->get()
                ->toArray();
            
            // استخدام الذكاء الاصطناعي لتوليد كلمات مفتاحية
            $response = $this->callAiService('generate-keywords', [
                'category' => [
                    'name' => $category->name,
                    'description' => $category->description
                ],
                'products' => $products
            ]);

            if (!$response || !isset($response['keywords'])) {
                return $this->fallbackKeywordGeneration($category);
            }

            return $response['keywords'];
        } catch (\Exception $e) {
            Log::error('Error generating category keywords: ' . $e->getMessage());
            return $this->fallbackKeywordGeneration($category);
        }
    }

    /**
     * طريقة احتياطية لاقتراح الفئات في حالة فشل خدمة الذكاء الاصطناعي
     *
     * @param array $productData بيانات المنتج
     * @param int $limit عدد الفئات المقترحة
     * @return array مصفوفة تحتوي على الفئات المقترحة
     */
    protected function fallbackCategorySuggestion(array $productData, int $limit): array
    {
        // استخدام البحث البسيط بالكلمات المفتاحية
        $searchTerms = [];
        
        if (isset($productData['name'])) {
            $searchTerms = array_merge($searchTerms, explode(' ', $productData['name']));
        }
        
        if (isset($productData['keywords']) && is_array($productData['keywords'])) {
            $searchTerms = array_merge($searchTerms, $productData['keywords']);
        }
        
        // تنظيف وإزالة التكرار من الكلمات المفتاحية
        $searchTerms = array_unique(array_filter($searchTerms, function ($term) {
            return strlen($term) > 3;
        }));
        
        // البحث عن الفئات المطابقة
        $categories = ProductCategory::where(function ($query) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $query->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            }
        })
        ->where('is_active', true)
        ->limit($limit)
        ->get();
        
        // تنسيق النتائج
        return $categories->map(function ($category) {
            return [
                'category_id' => $category->id,
                'name' => $category->name,
                'confidence' => 0.5 // قيمة افتراضية للثقة
            ];
        })->toArray();
    }

    /**
     * طريقة احتياطية لتوليد الكلمات المفتاحية في حالة فشل خدمة الذكاء الاصطناعي
     *
     * @param ProductCategory $category الفئة
     * @return array مصفوفة تحتوي على الكلمات المفتاحية
     */
    protected function fallbackKeywordGeneration(ProductCategory $category): array
    {
        $keywords = [];
        
        // استخراج كلمات من اسم الفئة
        $nameWords = explode(' ', $category->name);
        $keywords = array_merge($keywords, $nameWords);
        
        // استخراج كلمات من وصف الفئة إذا كان موجودًا
        if ($category->description) {
            $descWords = explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', $category->description));
            $descWords = array_filter($descWords, function ($word) {
                return strlen($word) > 3;
            });
            $keywords = array_merge($keywords, $descWords);
        }
        
        // إزالة التكرار وترتيب الكلمات المفتاحية
        $keywords = array_unique($keywords);
        
        return array_values($keywords);
    }

    /**
     * استدعاء خدمة الذكاء الاصطناعي الخارجية
     *
     * @param string $endpoint نقطة النهاية للخدمة
     * @param array $data البيانات المرسلة للخدمة
     * @return array|null الاستجابة من الخدمة
     */
    protected function callAiService(string $endpoint, array $data): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post("{$this->apiUrl}/{$endpoint}", $data);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            Log::warning("AI service returned error: {$response->status()}", [
                'endpoint' => $endpoint,
                'response' => $response->body()
            ]);
            
            return null;
        } catch (\Exception $e) {
            Log::error("Error calling AI service: {$e->getMessage()}", [
                'endpoint' => $endpoint,
                'exception' => $e
            ]);
            
            return null;
        }
    }
}
