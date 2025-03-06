<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\CustomerAuthController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// تضمين ملفات المسارات
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/messenger.php';
require __DIR__ . '/user.php';
require __DIR__ . '/supervisor.php';
require __DIR__ . '/agent.php';
require __DIR__ . '/customer.php';
require __DIR__ . '/merchant.php';
require __DIR__ . '/dashboard.php';

//==================================================
// المسارات العامة
//==================================================

// الصفحات الرئيسية
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/features', [HomeController::class, 'features'])->name('features');
Route::get('/app', [HomeController::class, 'app'])->name('app');
Route::get('/testimonials', [HomeController::class, 'testimonials'])->name('testimonials');

// صفحات المعلومات
Route::prefix('info')->group(function () {
    Route::get('/contact', function () {
        return view('pages.contact');
    })->name('contact');
    
    Route::get('/privacy', function () {
        return view('pages.privacy');
    })->name('privacy');
    
    Route::get('/terms', function () {
        return view('pages.terms');
    })->name('terms');
    
    Route::get('/cookies', function () {
        return view('pages.cookies');
    })->name('cookies');
    
    Route::get('/faq', function () {
        return view('pages.faq');
    })->name('faq');
});

//==================================================
// مسارات خدمات النظام
//==================================================

Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/wallet', [ServiceController::class, 'wallet'])->name('wallet');
    Route::get('/commerce', [ServiceController::class, 'commerce'])->name('commerce');
    Route::get('/messaging', [ServiceController::class, 'messaging'])->name('messaging');
    Route::get('/delivery', [ServiceController::class, 'delivery'])->name('delivery');
    Route::get('/ai-assistant', [ServiceController::class, 'aiAssistant'])->name('ai-assistant');
    Route::get('/agents', [ServiceController::class, 'agents'])->name('agents');
});

//==================================================
// مسارات حسابات العملاء
//==================================================

Route::prefix('customer')->name('customer.')->middleware(['auth', 'check-role:customer'])->group(function () {
    // لوحة التحكم
    Route::get('/dashboard', [CustomerAuthController::class, 'dashboard'])->name('dashboard');
    
    // المحفظة
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [CustomerAuthController::class, 'wallet'])->name('index');
        Route::get('/transactions', [CustomerAuthController::class, 'transactions'])->name('transactions');
        Route::post('/transfer', [CustomerAuthController::class, 'transfer'])->name('transfer');
        Route::get('/qr-code', [CustomerAuthController::class, 'qrCode'])->name('qr-code');
    });
    
    // الملف الشخصي
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [CustomerAuthController::class, 'profile'])->name('index');
        Route::put('/update', [CustomerAuthController::class, 'updateProfile'])->name('update');
        Route::put('/change-password', [CustomerAuthController::class, 'changePassword'])->name('change-password');
    });
    
    // الإعدادات
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [CustomerAuthController::class, 'settings'])->name('index');
        Route::put('/security', [CustomerAuthController::class, 'updateSecurity'])->name('security');
        Route::put('/notifications', [CustomerAuthController::class, 'updateNotifications'])->name('notifications');
    });
    
    // المراسلات
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [CustomerAuthController::class, 'messages'])->name('index');
        Route::get('/{id}', [CustomerAuthController::class, 'viewMessage'])->name('view');
        Route::post('/send', [CustomerAuthController::class, 'sendMessage'])->name('send');
    });
    
    // التسوق
    Route::prefix('shopping')->name('shopping.')->group(function () {
        Route::get('/products', [CustomerAuthController::class, 'products'])->name('products');
        Route::get('/orders', [CustomerAuthController::class, 'orders'])->name('orders');
        Route::get('/orders/{id}', [CustomerAuthController::class, 'orderDetails'])->name('order-details');
    });
});

//==================================================
// مسارات الإشعارات
//==================================================

Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/', [NotificationController::class, 'destroyAll'])->name('destroy-all');
    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    Route::get('/latest-unread', [NotificationController::class, 'getLatestUnread'])->name('latest-unread');
});

Route::get('/cities-by-country', function (Request $request) {
    $countryId = $request->input('country_id');
    $cities = App\Models\City::where('country_id', $countryId)
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->get();
    return response()->json($cities);
});

//==================================================
// مسارات مؤقتة للتطوير
//==================================================

Route::prefix('dev')->middleware(['auth', 'check-role:admin'])->group(function () {
    Route::get('/test-theme', [HomeController::class, 'testTheme'])->name('test-theme');
    Route::get('/temp-view/{view}', function ($view) {
        return view($view);
    });
});