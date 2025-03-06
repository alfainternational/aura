<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AI\KycVerificationService;
use App\Services\AI\Contracts\CategoryAnalysisServiceInterface;
use App\Services\AI\Contracts\RecommendationServiceInterface;
use App\Services\AI\Contracts\ContentModerationServiceInterface;
use App\Services\AI\Contracts\LanguageTranslationServiceInterface;
use App\Services\AI\Contracts\ProductAnalysisServiceInterface;
use App\Services\AI\Contracts\FraudDetectionServiceInterface;
use App\Services\AI\CategoryAnalysisService;
use App\Services\AI\ContentModerationService;
use App\Services\AI\LanguageTranslationService;
use App\Services\AI\ProductAnalysisService;
use App\Services\AI\FraudDetectionService;
use App\Services\AI\Implementations\RecommendationService;
use App\Services\AI\Contracts\BusinessInsightServiceInterface;
use App\Services\AI\BusinessInsightService;

class AIServiceProvider extends ServiceProvider
{
    /**
     * Register any AI services.
     */
    public function register(): void
    {
        // Registrar el servicio de verificación KYC con IA
        $this->app->singleton(KycVerificationService::class, function ($app) {
            return new KycVerificationService();
        });

        // Registrar el servicio de análisis de categorías
        $this->app->bind(CategoryAnalysisServiceInterface::class, CategoryAnalysisService::class);

        // Registrar el servicio de recomendaciones
        $this->app->bind(RecommendationServiceInterface::class, RecommendationService::class);
        
        // Registrar el servicio de moderación de contenido
        $this->app->bind(ContentModerationServiceInterface::class, ContentModerationService::class);
        
        // Registrar el servicio de traducción de idiomas
        $this->app->bind(LanguageTranslationServiceInterface::class, LanguageTranslationService::class);
        
        // Registrar el servicio de análisis de productos
        $this->app->bind(ProductAnalysisServiceInterface::class, ProductAnalysisService::class);
        
        // Registrar el servicio de detección de fraude
        $this->app->bind(FraudDetectionServiceInterface::class, FraudDetectionService::class);
        
        // Registrar el servicio de BusinessInsightService
        $this->app->bind(BusinessInsightServiceInterface::class, BusinessInsightService::class);
    }

    /**
     * Bootstrap any AI services.
     */
    public function boot(): void
    {
        //
    }
}
