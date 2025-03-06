<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class UrlFixServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // تعيين URL الأساسي للتطبيق
        // هذا يضمن أن وظيفة route() في Blade ستنتج روابط صحيحة
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        } else {
            URL::forceScheme('http');
            // عند استخدام XAMPP، نتأكد من استخدام القاعدة الصحيحة للروابط
            // هذا لضمان أن جميع الروابط التي تنشأ من route() ستكون صحيحة
            if (!app()->runningInConsole()) {
                URL::forceRootUrl(config('app.url') . '/aura/backend/public');
            }
        }
    }
}
