<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomerAuthController;

/*
|--------------------------------------------------------------------------
| مسارات العملاء
|--------------------------------------------------------------------------
|
| هنا يمكنك تسجيل مسارات العملاء للتطبيق.
|
*/

// مسارات العميل المحمية
Route::middleware(['auth', 'check-role:customer'])->prefix('customer')->name('customer.')->group(function () {
    // لوحة التحكم الرئيسية للعميل
    Route::get('/dashboard', [CustomerAuthController::class, 'dashboard'])->name('dashboard');

    // الملف الشخصي للعميل
    Route::get('/profile', [CustomerAuthController::class, 'profile'])->name('profile');

    // تحديث بيانات الملف الشخصي
    Route::put('/profile/update', [CustomerAuthController::class, 'updateProfile'])->name('profile.update');

    // عرض الطلبات السابقة
    Route::get('/orders', function () {
        return view('customer.orders');
    })->name('orders');

    // تفاصيل طلب محدد
    Route::get('/orders/{id}', function ($id) {
        return view('customer.order-details', ['id' => $id]);
    })->name('orders.show');

    // العناوين
    Route::get('/addresses', [CustomerAuthController::class, 'addresses'])->name('addresses');

    // إضافة عنوان جديد
    Route::post('/addresses', [CustomerAuthController::class, 'addAddress'])->name('addresses.store');

    // تحديث عنوان
    Route::put('/addresses/{id}', function ($id) {
        // منطق تحديث العنوان
        return response()->json(['message' => 'تم تحديث العنوان بنجاح']);
    })->name('addresses.update');

    // حذف عنوان
    Route::delete('/addresses/{id}', [CustomerAuthController::class, 'deleteAddress'])->name('addresses.delete');

    // تعيين عنوان كافتراضي
    Route::post('/addresses/{id}/default', [CustomerAuthController::class, 'setDefaultAddress'])->name('addresses.default');

    // الإشعارات
    Route::get('/notifications', function () {
        return view('customer.notifications');
    })->name('notifications');

    // تحديث إعدادات الإشعارات
    Route::put('/notification-settings', function () {
        // منطق تحديث إعدادات الإشعارات
        return response()->json(['message' => 'تم تحديث إعدادات الإشعارات بنجاح']);
    })->name('notification-settings.update');
});

// صفحة استكمال بيانات العميل (غير محمية بميدلوير الأدوار)
Route::middleware(['auth'])->group(function() {
    Route::get('/complete-profile', [CustomerAuthController::class, 'completeProfile'])->name('complete-profile');
    Route::post('/store-profile', [CustomerAuthController::class, 'storeProfile'])->name('store-profile');
});
