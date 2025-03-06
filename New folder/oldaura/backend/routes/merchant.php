<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\MerchantAuthController;

/*
|--------------------------------------------------------------------------
| مسارات التاجر
|--------------------------------------------------------------------------
|
| هنا يمكنك تسجيل مسارات التاجر للتطبيق. هذه المسارات يتم تحميلها
| من قبل RouteServiceProvider داخل مجموعة تحوي وسيط "web".
|
*/

// صفحة استكمال بيانات ملف التاجر
Route::middleware(['auth'])->group(function() {
    Route::get('/complete-profile', [MerchantAuthController::class, 'completeProfile'])->name('complete-profile');
    Route::post('/store-profile', [MerchantAuthController::class, 'storeProfile'])->name('store-profile');
});

// جميع مسارات التاجر المحمية
Route::middleware(['auth', 'check-role:merchant'])->group(function() {
    // لوحة التحكم الرئيسية للتاجر
    Route::get('/dashboard', [MerchantAuthController::class, 'dashboard'])->name('dashboard');

    // الملف الشخصي للتاجر
    Route::get('/profile', [MerchantAuthController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [MerchantAuthController::class, 'updateProfile'])->name('profile.update');

    // إدارة المنتجات
    Route::get('/products', function () {
        return view('merchant.products');
    })->name('products');

    // إضافة منتج جديد
    Route::get('/products/create', function () {
        return view('merchant.products.create');
    })->name('products.create');

    // تخزين منتج جديد
    Route::post('/products', function () {
        // منطق تخزين منتج جديد
    })->name('products.store');

    // تعديل منتج
    Route::get('/products/{id}/edit', function ($id) {
        return view('merchant.products.edit', ['id' => $id]);
    })->name('products.edit');

    // تحديث منتج
    Route::put('/products/{id}', function ($id) {
        // منطق تحديث منتج
    })->name('products.update');

    // حذف منتج
    Route::delete('/products/{id}', function ($id) {
        // منطق حذف منتج
    })->name('products.destroy');

    // إدارة الطلبات
    Route::get('/orders', function () {
        return view('merchant.orders');
    })->name('orders');

    // تفاصيل طلب
    Route::get('/orders/{id}', function ($id) {
        return view('merchant.orders.show', ['id' => $id]);
    })->name('orders.show');

    // تحديث حالة طلب
    Route::put('/orders/{id}/status', function ($id) {
        // منطق تحديث حالة طلب
    })->name('orders.update-status');

    // التقارير والإحصائيات
    Route::get('/reports', function () {
        return view('merchant.reports');
    })->name('reports');

    // تقرير المبيعات
    Route::get('/reports/sales', function () {
        return view('merchant.reports.sales');
    })->name('reports.sales');

    // تقرير المنتجات الأكثر مبيعاً
    Route::get('/reports/top-products', function () {
        return view('merchant.reports.top-products');
    })->name('reports.top-products');

    // إعدادات الحساب
    Route::get('/settings', function () {
        return view('merchant.settings');
    })->name('settings');

    // تحديث إعدادات الحساب
    Route::put('/settings', function () {
        // منطق تحديث إعدادات الحساب
    })->name('settings.update');
});
