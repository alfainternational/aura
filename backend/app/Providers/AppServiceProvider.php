<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use App\Models\User;
use App\Services\OtpService;
use App\Helpers\CurrencyHelper;
use App\Models\Country;

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
        
        // إضافة مساعدات Blade لتنسيق العملات
        Blade::directive('currency', function ($expression) {
            return "<?php echo App\Helpers\CurrencyHelper::formatAmount($expression); ?>";
        });
        
        // تسجيل عامل مشاهدة Blade مخصص للعملة الافتراضية
        Blade::directive('defaultCurrency', function () {
            return "<?php echo App\Models\Country::getDefaultCurrency(); ?>";
        });
        
        // تسجيل عامل مشاهدة Blade مخصص لرمز العملة الافتراضية
        Blade::directive('defaultCurrencySymbol', function () {
            return "<?php echo App\Models\Country::getDefaultCurrencySymbol(); ?>";
        });
    }
}
