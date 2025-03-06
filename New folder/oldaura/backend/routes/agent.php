<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AgentAuthController;

/*
|--------------------------------------------------------------------------
| مسارات الوكيل
|--------------------------------------------------------------------------
|
| هنا يمكنك تسجيل مسارات الوكيل للتطبيق. هذه المسارات يتم تحميلها
| من قبل RouteServiceProvider داخل مجموعة تحوي وسيط "web".
|
*/

// صفحة استكمال بيانات ملف الوكيل
Route::middleware(['auth'])->group(function() {
    Route::get('/complete-profile', [AgentAuthController::class, 'completeProfile'])->name('complete-profile');
    Route::post('/store-profile', [AgentAuthController::class, 'storeProfile'])->name('store-profile');
});

// مسارات الوكيل المحمية
Route::middleware(['auth', 'check-role:agent'])->group(function() {
    // لوحة التحكم الرئيسية للوكيل
    Route::get('/dashboard', [AgentAuthController::class, 'dashboard'])->name('dashboard');

    // الملف الشخصي للوكيل
    Route::get('/profile', [AgentAuthController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [AgentAuthController::class, 'updateProfile'])->name('profile.update');

    // إدارة العملاء
    Route::get('/customers', function () {
        return view('agent.customers');
    })->name('customers');

    // عرض تفاصيل عميل
    Route::get('/customers/{id}', function ($id) {
        return view('agent.customers.show', ['id' => $id]);
    })->name('customers.show');

    // إضافة عميل جديد
    Route::get('/customers/create', function () {
        return view('agent.customers.create');
    })->name('customers.create');

    // تخزين عميل جديد
    Route::post('/customers', function () {
        // منطق تخزين عميل جديد
    })->name('customers.store');

    // إدارة الطلبات
    Route::get('/orders', function () {
        return view('agent.orders');
    })->name('orders');

    // تفاصيل طلب
    Route::get('/orders/{id}', function ($id) {
        return view('agent.orders.show', ['id' => $id]);
    })->name('orders.show');

    // تحديث حالة طلب
    Route::put('/orders/{id}/status', function ($id) {
        // منطق تحديث حالة طلب
    })->name('orders.update-status');

    // المحفظة
    Route::get('/wallet', function () {
        return view('agent.wallet');
    })->name('wallet');

    // المعاملات
    Route::get('/transactions', function () {
        return view('agent.transactions');
    })->name('transactions');

    // الإحصائيات
    Route::get('/statistics', function () {
        return view('agent.statistics');
    })->name('statistics');

    // الإعدادات
    Route::get('/settings', function () {
        return view('agent.settings');
    })->name('settings');

    // تحديث الإعدادات
    Route::put('/settings', function () {
        // منطق تحديث الإعدادات
    })->name('settings.update');
});
