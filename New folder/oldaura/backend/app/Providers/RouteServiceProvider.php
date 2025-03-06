<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * مسارات الصفحة الرئيسية للأنواع المختلفة من المستخدمين
     *
     * @var array
     */
    public static $HOME_ROUTES = [
        'user' => '/dashboard/user',
        'customer' => '/customer/dashboard',
        'merchant' => '/merchant/dashboard',
        'agent' => '/agent/dashboard',
        'messenger' => '/messenger/dashboard',
        'admin' => '/admin/dashboard',
        'supervisor' => '/supervisor/dashboard',
    ];

    /**
     * المسار الافتراضي للصفحة الرئيسية.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * تحديد روابط نماذج المسارات، ومرشحات الأنماط، وإعدادات المسار الأخرى.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
                
            // مسارات المصادقة للمشرفين
            Route::middleware('web')
                ->group(base_path('routes/admin-auth.php'));
                
            // مسارات لوحات التحكم لجميع أنواع المستخدمين
            Route::middleware(['web', 'auth'])
                ->group(base_path('routes/dashboard.php'));
                
            // مسارات لوحة تحكم المسؤول
            Route::middleware(['web', 'auth', 'check-role:admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
                
            // مسارات لوحة تحكم المشرف
            Route::middleware(['web', 'auth', 'check-role:admin,supervisor'])
                ->prefix('supervisor')
                ->name('supervisor.')
                ->group(base_path('routes/supervisor.php'));
                
            // مسارات لوحة تحكم المندوب
            Route::middleware(['web', 'auth', 'check-role:messenger'])
                ->prefix('messenger')
                ->name('messenger.')
                ->group(base_path('routes/messenger.php'));
                
            // مسارات لوحة تحكم العميل
            Route::middleware(['web', 'auth', 'check-role:customer'])
                ->prefix('customer')
                ->name('customer.')
                ->group(base_path('routes/customer.php'));
                
            // مسارات لوحة تحكم التاجر
            Route::middleware(['web', 'auth', 'check-role:merchant'])
                ->prefix('merchant')
                ->name('merchant.')
                ->group(base_path('routes/merchant.php'));
                
            // مسارات لوحة تحكم الوكيل
            Route::middleware(['web', 'auth', 'check-role:agent'])
                ->prefix('agent')
                ->name('agent.')
                ->group(base_path('routes/agent.php'));
        });
    }
    
    /**
     * Get the appropriate home route for a user.
     *
     * @param \App\Models\User $user
     * @return string
     */
    public static function getHomeRoute($user)
    {
        if (!$user) {
            return '/login';
        }

        // Utilizamos las rutas definidas en $HOME_ROUTES para asegurar consistencia
        $routePath = self::$HOME_ROUTES[$user->user_type] ?? '/home';
        return $routePath;
    }
}
