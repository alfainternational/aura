<?php

namespace App\Services\AI;

use App\Models\Product;
use App\Models\SmartCategory;
use App\Models\CategoryFeedback;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SmartCategoryService
{
    /**
     * API URL para el servicio de IA
     */
    protected $apiUrl;
    
    /**
     * API Key para el servicio de IA
     */
    protected $apiKey;
    
    /**
     * Modelo de IA a utilizar
     */
    protected $model;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->apiUrl = config('services.ai.url');
        $this->apiKey = config('services.ai.key');
        $this->model = config('services.ai.model', 'gpt-4');
    }
    
    /**
     * Sugerir categorías para un producto específico
     */
    public function suggestCategoriesForProduct(Product $product, $limit = 5)
    {
        try {
            // Preparar los datos del producto para el análisis
            $productData = [
                'name' => $product->name,
                'description' => $product->description,
                'attributes' => $product->attributes,
                'tags' => $product->tags,
                'price' => $product->price,
            ];
            
            // Verificar si hay resultados en caché
            $cacheKey = 'product_category_suggestions_' . $product->id;
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            
            // Preparar el prompt para el modelo de IA
            $prompt = $this->buildProductCategoryPrompt($productData);
            
            // Realizar la llamada a la API
            $response = $this->callAiApi($prompt);
            
            // Procesar la respuesta
            $suggestions = $this->processCategorySuggestions($response, $limit);
            
            // Guardar en caché por 24 horas
            Cache::put($cacheKey, $suggestions, 60 * 24);
            
            return $suggestions;
        } catch (\Exception $e) {
            Log::error('Error al sugerir categorías para el producto: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generar nuevas categorías basadas en un conjunto de productos
     */
    public function generateCategoriesFromProducts(array $products, $limit = 10)
    {
        try {
            // Preparar los datos de los productos para el análisis
            $productsData = [];
            foreach ($products as $product) {
                $productsData[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'attributes' => $product->attributes,
                    'tags' => $product->tags,
                ];
            }
            
            // Preparar el prompt para el modelo de IA
            $prompt = $this->buildGenerateCategoriesPrompt($productsData);
            
            // Realizar la llamada a la API
            $response = $this->callAiApi($prompt);
            
            // Procesar la respuesta
            $categories = $this->processGeneratedCategories($response, $limit);
            
            return $categories;
        } catch (\Exception $e) {
            Log::error('Error al generar categorías: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Analizar la similitud entre categorías para sugerir fusiones
     */
    public function analyzeCategorySimilarity($threshold = 0.7)
    {
        try {
            $categories = SmartCategory::all();
            $similarPairs = [];
            
            // Comparar cada par de categorías
            foreach ($categories as $category1) {
                foreach ($categories as $category2) {
                    // No comparar una categoría consigo misma
                    if ($category1->id == $category2->id) {
                        continue;
                    }
                    
                    // Calcular similitud
                    $similarity = $this->calculateCategorySimilarity($category1, $category2);
                    
                    // Si la similitud supera el umbral, agregar a la lista
                    if ($similarity >= $threshold) {
                        $similarPairs[] = [
                            'source' => $category1,
                            'target' => $category2,
                            'similarity' => $similarity,
                        ];
                    }
                }
            }
            
            // Ordenar por similitud (de mayor a menor)
            usort($similarPairs, function ($a, $b) {
                return $b['similarity'] <=> $a['similarity'];
            });
            
            return $similarPairs;
        } catch (\Exception $e) {
            Log::error('Error al analizar similitud de categorías: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Sugerir reorganización de categorías
     */
    public function suggestCategoryReorganization()
    {
        try {
            $categories = SmartCategory::all();
            
            // Preparar los datos de las categorías para el análisis
            $categoriesData = [];
            foreach ($categories as $category) {
                $categoriesData[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'parent_id' => $category->parent_id,
                    'product_count' => $category->products->count(),
                ];
            }
            
            // Preparar el prompt para el modelo de IA
            $prompt = $this->buildReorganizationPrompt($categoriesData);
            
            // Realizar la llamada a la API
            $response = $this->callAiApi($prompt);
            
            // Procesar la respuesta
            $suggestions = $this->processReorganizationSuggestions($response);
            
            return $suggestions;
        } catch (\Exception $e) {
            Log::error('Error al sugerir reorganización de categorías: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Clasificar automáticamente un producto
     */
    public function autoClassifyProduct(Product $product)
    {
        try {
            // Obtener sugerencias de categorías
            $suggestions = $this->suggestCategoriesForProduct($product, 3);
            
            if (empty($suggestions)) {
                return false;
            }
            
            // Seleccionar la categoría con mayor confianza
            $bestSuggestion = $suggestions[0];
            
            // Buscar o crear la categoría
            $category = SmartCategory::firstOrCreate(
                ['name' => $bestSuggestion['name']],
                [
                    'slug' => Str::slug($bestSuggestion['name']),
                    'description' => $bestSuggestion['description'] ?? '',
                    'confidence_score' => $bestSuggestion['confidence'],
                    'ai_generated' => true,
                    'attributes' => $bestSuggestion['attributes'] ?? [],
                ]
            );
            
            // Asignar el producto a la categoría
            $product->smartCategories()->syncWithoutDetaching([$category->id]);
            
            return $category;
        } catch (\Exception $e) {
            Log::error('Error al clasificar automáticamente el producto: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Procesar feedback del usuario para mejorar el modelo
     */
    public function processFeedback(CategoryFeedback $feedback)
    {
        try {
            $category = $feedback->smartCategory;
            
            // Actualizar la puntuación de confianza de la categoría
            $feedbackValue = $feedback->is_helpful ? 0.05 : -0.05;
            $category->updateConfidenceScore($feedbackValue);
            
            // Si hay un producto asociado, usar el feedback para mejorar
            // las futuras clasificaciones
            if ($feedback->product_id) {
                $product = $feedback->product;
                
                // Preparar los datos para el entrenamiento
                $trainingData = [
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'attributes' => $product->attributes,
                    ],
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->name,
                    ],
                    'feedback' => [
                        'is_helpful' => $feedback->is_helpful,
                        'rating' => $feedback->rating,
                        'comment' => $feedback->comment,
                    ],
                ];
                
                // Guardar los datos de entrenamiento en la categoría
                $category->training_data = array_merge($category->training_data ?? [], [$trainingData]);
                $category->save();
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error al procesar feedback: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Construir el prompt para sugerir categorías para un producto
     */
    protected function buildProductCategoryPrompt($productData)
    {
        return "Analiza el siguiente producto y sugiere las categorías más adecuadas para él. " .
               "Para cada categoría, proporciona un nombre, una breve descripción, una puntuación de confianza (0-1) y atributos relevantes.\n\n" .
               "Producto:\n" .
               "Nombre: " . $productData['name'] . "\n" .
               "Descripción: " . $productData['description'] . "\n" .
               "Atributos: " . json_encode($productData['attributes']) . "\n" .
               "Etiquetas: " . json_encode($productData['tags']) . "\n\n" .
               "Formato de respuesta (JSON):\n" .
               "[\n" .
               "  {\n" .
               "    \"name\": \"Nombre de la categoría\",\n" .
               "    \"description\": \"Breve descripción\",\n" .
               "    \"confidence\": 0.95,\n" .
               "    \"attributes\": [\"atributo1\", \"atributo2\"]\n" .
               "  },\n" .
               "  ...\n" .
               "]";
    }
    
    /**
     * Construir el prompt para generar categorías a partir de productos
     */
    protected function buildGenerateCategoriesPrompt($productsData)
    {
        $productsJson = json_encode($productsData, JSON_PRETTY_PRINT);
        
        return "Analiza los siguientes productos y genera categorías lógicas para agruparlos. " .
               "Para cada categoría, proporciona un nombre, una descripción, atributos relevantes y los IDs de los productos que pertenecerían a esa categoría.\n\n" .
               "Productos:\n" . $productsJson . "\n\n" .
               "Formato de respuesta (JSON):\n" .
               "[\n" .
               "  {\n" .
               "    \"name\": \"Nombre de la categoría\",\n" .
               "    \"description\": \"Descripción detallada\",\n" .
               "    \"attributes\": [\"atributo1\", \"atributo2\"],\n" .
               "    \"product_ids\": [1, 2, 3]\n" .
               "  },\n" .
               "  ...\n" .
               "]";
    }
    
    /**
     * Construir el prompt para sugerir reorganización de categorías
     */
    protected function buildReorganizationPrompt($categoriesData)
    {
        $categoriesJson = json_encode($categoriesData, JSON_PRETTY_PRINT);
        
        return "Analiza la siguiente estructura de categorías y sugiere mejoras en la organización. " .
               "Identifica categorías que deberían fusionarse, dividirse o reubicarse en la jerarquía.\n\n" .
               "Categorías actuales:\n" . $categoriesJson . "\n\n" .
               "Formato de respuesta (JSON):\n" .
               "[\n" .
               "  {\n" .
               "    \"type\": \"merge\",\n" .
               "    \"source_id\": 5,\n" .
               "    \"target_id\": 3,\n" .
               "    \"reason\": \"Estas categorías contienen productos similares\"\n" .
               "  },\n" .
               "  {\n" .
               "    \"type\": \"move\",\n" .
               "    \"category_id\": 7,\n" .
               "    \"new_parent_id\": 2,\n" .
               "    \"reason\": \"Esta categoría es más relevante como subcategoría de...\"\n" .
               "  },\n" .
               "  {\n" .
               "    \"type\": \"split\",\n" .
               "    \"category_id\": 4,\n" .
               "    \"new_categories\": [\n" .
               "      {\"name\": \"Nueva categoría 1\", \"description\": \"...\"},\n" .
               "      {\"name\": \"Nueva categoría 2\", \"description\": \"...\"}\n" .
               "    ],\n" .
               "    \"reason\": \"Esta categoría es demasiado amplia y contiene productos diversos\"\n" .
               "  }\n" .
               "]";
    }
    
    /**
     * Calcular la similitud entre dos categorías
     */
    protected function calculateCategorySimilarity(SmartCategory $category1, SmartCategory $category2)
    {
        // Implementación básica de similitud basada en nombre, descripción y atributos
        $nameWeight = 0.4;
        $descriptionWeight = 0.3;
        $attributesWeight = 0.3;
        
        // Similitud de nombre (usando distancia de Levenshtein normalizada)
        $nameSimilarity = 1 - (levenshtein($category1->name, $category2->name) / max(strlen($category1->name), strlen($category2->name)));
        
        // Similitud de descripción
        $descriptionSimilarity = 0;
        if (!empty($category1->description) && !empty($category2->description)) {
            $descriptionSimilarity = 1 - (levenshtein($category1->description, $category2->description) / max(strlen($category1->description), strlen($category2->description)));
        }
        
        // Similitud de atributos
        $attributesSimilarity = 0;
        if (!empty($category1->attributes) && !empty($category2->attributes)) {
            $commonAttributes = array_intersect($category1->attributes, $category2->attributes);
            $allAttributes = array_unique(array_merge($category1->attributes, $category2->attributes));
            $attributesSimilarity = count($commonAttributes) / count($allAttributes);
        }
        
        // Calcular similitud ponderada
        $similarity = ($nameWeight * $nameSimilarity) + 
                      ($descriptionWeight * $descriptionSimilarity) + 
                      ($attributesWeight * $attributesSimilarity);
        
        return $similarity;
    }
    
    /**
     * Realizar llamada a la API de IA
     */
    protected function callAiApi($prompt)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un asistente especializado en categorización de productos para e-commerce.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 2000,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? '';
            } else {
                Log::error('Error en la API de IA: ' . $response->body());
                return '';
            }
        } catch (\Exception $e) {
            Log::error('Error al llamar a la API de IA: ' . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Procesar las sugerencias de categorías
     */
    protected function processCategorySuggestions($response, $limit)
    {
        try {
            $data = json_decode($response, true);
            
            if (!is_array($data)) {
                return [];
            }
            
            // Limitar el número de sugerencias
            $data = array_slice($data, 0, $limit);
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Error al procesar sugerencias de categorías: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Procesar las categorías generadas
     */
    protected function processGeneratedCategories($response, $limit)
    {
        try {
            $data = json_decode($response, true);
            
            if (!is_array($data)) {
                return [];
            }
            
            // Limitar el número de categorías
            $data = array_slice($data, 0, $limit);
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Error al procesar categorías generadas: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Procesar las sugerencias de reorganización
     */
    protected function processReorganizationSuggestions($response)
    {
        try {
            $data = json_decode($response, true);
            
            if (!is_array($data)) {
                return [];
            }
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Error al procesar sugerencias de reorganización: ' . $e->getMessage());
            return [];
        }
    }
}
