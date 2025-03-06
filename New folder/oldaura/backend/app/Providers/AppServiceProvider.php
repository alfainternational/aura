<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Services\OtpService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar el servicio OTP
        $this->app->singleton(OtpService::class, function ($app) {
            return new OtpService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تعريف التصاريح للأدوار المختلفة
        Gate::define('admin-access', function (User $user) {
            return $user->user_type === 'admin';
        });
        
        Gate::define('supervisor-access', function (User $user) {
            return $user->user_type === 'admin' && $user->role === 'supervisor';
        });
        
        Gate::define('messenger-access', function (User $user) {
            return $user->user_type === 'messenger';
        });
        
        Gate::define('merchant-access', function (User $user) {
            return $user->user_type === 'merchant';
        });
        
        Gate::define('agent-access', function (User $user) {
            return $user->user_type === 'agent';
        });
        
        Gate::define('customer-access', function (User $user) {
            return $user->user_type === 'customer';
        });
    }
}
