<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\ProductAnalysisServiceInterface;
use Illuminate\Support\Facades\Log;

class ProductAnalysisService implements ProductAnalysisServiceInterface
{
    /**
     * تحليل بيانات المنتج واقتراح تحسينات
     * 
     * @param array $productData بيانات المنتج
     * @return array اقتراحات التحسين
     */
    public function analyzeProduct(array $productData): array
    {
        try {
            $suggestions = [];
            
            // Analizar el título del producto
            if (isset($productData['name'])) {
                $titleSuggestions = $this->analyzeTitleQuality($productData['name']);
                if (!empty($titleSuggestions)) {
                    $suggestions['title'] = $titleSuggestions;
                }
            }
            
            // Analizar la descripción del producto
            if (isset($productData['description'])) {
                $descriptionSuggestions = $this->analyzeDescriptionQuality($productData['description']);
                if (!empty($descriptionSuggestions)) {
                    $suggestions['description'] = $descriptionSuggestions;
                }
            }
            
            // Analizar el precio
            if (isset($productData['price'])) {
                $priceSuggestions = $this->analyzePricing($productData['price'], $productData);
                if (!empty($priceSuggestions)) {
                    $suggestions['price'] = $priceSuggestions;
                }
            }
            
            // Analizar las imágenes
            if (isset($productData['images']) && is_array($productData['images'])) {
                $imageSuggestions = $this->evaluateProductImages($productData['images']);
                if (!empty($imageSuggestions)) {
                    $suggestions['images'] = $imageSuggestions;
                }
            }
            
            // Analizar categorización
            if (isset($productData['category_id'])) {
                $categorySuggestions = $this->analyzeCategorization($productData);
                if (!empty($categorySuggestions)) {
                    $suggestions['category'] = $categorySuggestions;
                }
            }
            
            return [
                'product_id' => $productData['id'] ?? null,
                'suggestions' => $suggestions,
                'overall_score' => $this->calculateOverallScore($suggestions),
                'keywords' => $this->suggestKeywords($productData),
            ];
        } catch (\Exception $e) {
            Log::error('Error analyzing product: ' . $e->getMessage());
            return [
                'product_id' => $productData['id'] ?? null,
                'error' => 'Error al analizar el producto',
                'suggestions' => [],
                'overall_score' => 0,
                'keywords' => [],
            ];
        }
    }
    
    /**
     * تحسين وصف المنتج
     * 
     * @param string $description الوصف الحالي
     * @param array $productAttributes سمات المنتج
     * @return string الوصف المحسن
     */
    public function enhanceProductDescription(string $description, array $productAttributes): string
    {
        try {
            // En un entorno de producción, aquí se llamaría a un servicio de IA
            // para mejorar la descripción del producto
            
            // Para simplificar, simularemos una mejora básica
            if (empty(trim($description))) {
                // Si la descripción está vacía, generamos una basada en los atributos
                $enhancedDescription = "Este producto ";
                
                if (!empty($productAttributes['name'])) {
                    $enhancedDescription .= "\"" . $productAttributes['name'] . "\" ";
                }
                
                if (!empty($productAttributes['brand'])) {
                    $enhancedDescription .= "de la marca " . $productAttributes['brand'] . " ";
                }
                
                if (!empty($productAttributes['features']) && is_array($productAttributes['features'])) {
                    $enhancedDescription .= "ofrece las siguientes características: " . implode(", ", $productAttributes['features']) . ". ";
                }
                
                if (!empty($productAttributes['benefits']) && is_array($productAttributes['benefits'])) {
                    $enhancedDescription .= "Sus principales beneficios incluyen: " . implode(", ", $productAttributes['benefits']) . ". ";
                }
                
                $enhancedDescription .= "Ideal para quienes buscan calidad y rendimiento.";
                
                return $enhancedDescription;
            } else {
                // Si ya hay una descripción, la mejoramos
                $enhancedDescription = $description;
                
                // Asegurarse de que termina con un punto
                if (!in_array(substr($enhancedDescription, -1), ['.', '!', '?'])) {
                    $enhancedDescription .= '.';
                }
                
                // Añadir información adicional si está disponible
                if (!empty($productAttributes['benefits']) && is_array($productAttributes['benefits'])) {
                    $enhancedDescription .= " Entre sus beneficios destacan: " . implode(", ", $productAttributes['benefits']) . ".";
                }
                
                if (!empty($productAttributes['ideal_for']) && is_array($productAttributes['ideal_for'])) {
                    $enhancedDescription .= " Ideal para: " . implode(", ", $productAttributes['ideal_for']) . ".";
                }
                
                return $enhancedDescription;
            }
        } catch (\Exception $e) {
            Log::error('Error enhancing product description: ' . $e->getMessage());
            return $description; // Devolver la descripción original en caso de error
        }
    }
    
    /**
     * اقتراح كلمات مفتاحية للمنتج
     * 
     * @param array $productData بيانات المنتج
     * @return array الكلمات المفتاحية المقترحة
     */
    public function suggestKeywords(array $productData): array
    {
        try {
            $keywords = [];
            
            // Extraer palabras clave del nombre
            if (!empty($productData['name'])) {
                $nameKeywords = $this->extractKeywordsFromText($productData['name']);
                $keywords = array_merge($keywords, $nameKeywords);
            }
            
            // Extraer palabras clave de la descripción
            if (!empty($productData['description'])) {
                $descKeywords = $this->extractKeywordsFromText($productData['description']);
                $keywords = array_merge($keywords, $descKeywords);
            }
            
            // Añadir la categoría como palabra clave
            if (!empty($productData['category_name'])) {
                $keywords[] = $productData['category_name'];
            }
            
            // Añadir la marca como palabra clave
            if (!empty($productData['brand'])) {
                $keywords[] = $productData['brand'];
            }
            
            // Añadir atributos específicos
            if (!empty($productData['attributes']) && is_array($productData['attributes'])) {
                foreach ($productData['attributes'] as $attribute) {
                    if (is_string($attribute)) {
                        $keywords[] = $attribute;
                    } elseif (is_array($attribute) && !empty($attribute['value'])) {
                        $keywords[] = $attribute['value'];
                    }
                }
            }
            
            // Eliminar duplicados y palabras vacías
            $keywords = array_unique(array_filter($keywords));
            
            // Limitar a 15 palabras clave
            return array_slice($keywords, 0, 15);
        } catch (\Exception $e) {
            Log::error('Error suggesting keywords: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * تقييم جودة صور المنتج
     * 
     * @param array $imageUrls مسارات الصور
     * @return array تقييم جودة الصور
     */
    public function evaluateProductImages(array $imageUrls): array
    {
        try {
            $evaluation = [
                'has_images' => !empty($imageUrls),
                'image_count' => count($imageUrls),
                'suggestions' => []
            ];
            
            // Evaluar la cantidad de imágenes
            if (count($imageUrls) == 0) {
                $evaluation['suggestions'][] = [
                    'type' => 'critical',
                    'message' => 'El producto no tiene imágenes. Se recomienda añadir al menos una imagen principal.'
                ];
            } elseif (count($imageUrls) < 3) {
                $evaluation['suggestions'][] = [
                    'type' => 'improvement',
                    'message' => 'Se recomienda añadir más imágenes para mostrar diferentes ángulos y detalles del producto.'
                ];
            }
            
            // En un entorno real, aquí evaluaríamos la calidad de cada imagen
            // utilizando servicios de IA para detectar problemas como baja resolución,
            // mala iluminación, etc.
            
            return $evaluation;
        } catch (\Exception $e) {
            Log::error('Error evaluating product images: ' . $e->getMessage());
            return [
                'has_images' => !empty($imageUrls),
                'image_count' => count($imageUrls),
                'error' => 'Error al evaluar las imágenes',
                'suggestions' => []
            ];
        }
    }
    
    /**
     * Analizar la calidad del título del producto
     * 
     * @param string $title Título del producto
     * @return array Sugerencias para mejorar el título
     */
    private function analyzeTitleQuality(string $title): array
    {
        $suggestions = [];
        $title = trim($title);
        
        // Verificar longitud del título
        if (strlen($title) < 20) {
            $suggestions[] = [
                'type' => 'improvement',
                'message' => 'El título es demasiado corto. Se recomienda un título descriptivo de al menos 20 caracteres.'
            ];
        } elseif (strlen($title) > 100) {
            $suggestions[] = [
                'type' => 'improvement',
                'message' => 'El título es demasiado largo. Se recomienda acortarlo a menos de 100 caracteres.'
            ];
        }
        
        // Verificar si el título está en mayúsculas
        if (strtoupper($title) === $title) {
            $suggestions[] = [
                'type' => 'improvement',
                'message' => 'Evite usar MAYÚSCULAS en todo el título. Utilice mayúsculas solo al inicio de palabras importantes.'
            ];
        }
        
        return $suggestions;
    }
    
    /**
     * Analizar la calidad de la descripción del producto
     * 
     * @param string $description Descripción del producto
     * @return array Sugerencias para mejorar la descripción
     */
    private function analyzeDescriptionQuality(string $description): array
    {
        $suggestions = [];
        $description = trim($description);
        
        // Verificar si la descripción está vacía o es muy corta
        if (empty($description)) {
            $suggestions[] = [
                'type' => 'critical',
                'message' => 'El producto no tiene descripción. Se recomienda añadir una descripción detallada.'
            ];
        } elseif (strlen($description) < 100) {
            $suggestions[] = [
                'type' => 'improvement',
                'message' => 'La descripción es demasiado corta. Se recomienda una descripción de al menos 100 caracteres.'
            ];
        }
        
        // Verificar si la descripción contiene párrafos
        if (strlen($description) > 200 && !preg_match('/[\n\r]/', $description)) {
            $suggestions[] = [
                'type' => 'improvement',
                'message' => 'La descripción es un bloque de texto sin párrafos. Se recomienda dividirla en párrafos para mejorar la legibilidad.'
            ];
        }
        
        return $suggestions;
    }
    
    /**
     * Analizar el precio del producto
     * 
     * @param float $price Precio del producto
     * @param array $productData Datos completos del producto
     * @return array Sugerencias sobre el precio
     */
    private function analyzePricing(float $price, array $productData): array
    {
        $suggestions = [];
        
        // Verificar si el precio es cero o negativo
        if ($price <= 0) {
            $suggestions[] = [
                'type' => 'critical',
                'message' => 'El precio del producto es cero o negativo. Se debe establecer un precio válido.'
            ];
        }
        
        // En un entorno real, aquí compararíamos con productos similares
        // para sugerir ajustes de precio basados en el mercado
        
        return $suggestions;
    }
    
    /**
     * Analizar la categorización del producto
     * 
     * @param array $productData Datos del producto
     * @return array Sugerencias sobre la categorización
     */
    private function analyzeCategorization(array $productData): array
    {
        $suggestions = [];
        
        // En un entorno real, aquí analizaríamos si la categoría asignada
        // es la más adecuada para el producto basándonos en sus características
        
        return $suggestions;
    }
    
    /**
     * Extraer palabras clave de un texto
     * 
     * @param string $text Texto a analizar
     * @return array Palabras clave extraídas
     */
    private function extractKeywordsFromText(string $text): array
    {
        // Eliminar caracteres especiales y convertir a minúsculas
        $text = strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text));
        
        // Dividir en palabras
        $words = preg_split('/\s+/', $text);
        
        // Filtrar palabras vacías y demasiado cortas
        $words = array_filter($words, function($word) {
            return strlen($word) > 3;
        });
        
        // Contar frecuencia de palabras
        $wordCount = array_count_values($words);
        
        // Ordenar por frecuencia
        arsort($wordCount);
        
        // Devolver las claves (palabras)
        return array_keys($wordCount);
    }
    
    /**
     * Calcular una puntuación general basada en las sugerencias
     * 
     * @param array $suggestions Sugerencias de mejora
     * @return int Puntuación general (0-100)
     */
    private function calculateOverallScore(array $suggestions): int
    {
        $score = 100;
        $criticalCount = 0;
        $improvementCount = 0;
        
        // Contar problemas críticos y mejoras
        foreach ($suggestions as $category => $categorySuggestions) {
            foreach ($categorySuggestions as $suggestion) {
                if ($suggestion['type'] === 'critical') {
                    $criticalCount++;
                } elseif ($suggestion['type'] === 'improvement') {
                    $improvementCount++;
                }
            }
        }
        
        // Restar puntos por cada problema
        $score -= ($criticalCount * 15); // -15 puntos por cada problema crítico
        $score -= ($improvementCount * 5); // -5 puntos por cada mejora sugerida
        
        // Asegurar que la puntuación esté entre 0 y 100
        return max(0, min(100, $score));
    }
}
