<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\Role;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\SecurityAnalyzer::class, // Analizador de seguridad para detectar intrusiones
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\CountrySettingsMiddleware::class, // Aplicar configuración de país predeterminado
            \App\Http\Middleware\ApplyUserUiSettings::class, // Aplicar configuraciones de UI del usuario
        ],

        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SecurityAnalyzer::class, // Analizador de seguridad para detectar intrusiones
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // Authentication & Authorization
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Auth\Middleware\AuthenticateSession::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'redirectIfAuthenticated' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        
        // Role Management - Unified middleware for role checking
        'checkrole' => \App\Http\Middleware\CheckRole::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        'check-role' => \App\Http\Middleware\CheckRole::class,
        
        // Security & Verification
        '2fa' => \App\Http\Middleware\RequireTwoFactorAuth::class,
        '2fa.verify' => \App\Http\Middleware\TwoFactorVerify::class,
        'kyc.verified' => \App\Http\Middleware\RequireKycVerification::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'security.analyzer' => \App\Http\Middleware\SecurityAnalyzer::class,
        
        // Country & Currency Settings
        'country.settings' => \App\Http\Middleware\CountrySettingsMiddleware::class,
        
        // Performance & Throttling
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
    ];
}