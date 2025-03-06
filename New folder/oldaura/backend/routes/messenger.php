<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Messenger\MessengerController;
use App\Http\Controllers\Auth\MessengerAuthController;

/*
|--------------------------------------------------------------------------
| Messenger Routes
|--------------------------------------------------------------------------
|
| هنا يمكنك تسجيل مسارات المناديب
|
*/

// صفحة استكمال بيانات المندوب
Route::middleware(['auth'])->group(function() {
    Route::get('/complete-profile', [MessengerAuthController::class, 'completeProfile'])->name('complete-profile');
    Route::post('/store-profile', [MessengerAuthController::class, 'storeProfile'])->name('store-profile');
});

// مسارات المناديب
Route::middleware(['auth', 'check-role:messenger'])->prefix('messenger')->name('messenger.')->group(function () {
    // لوحة التحكم الرئيسية
    Route::get('/dashboard', [MessengerController::class, 'dashboard'])->name('dashboard');
    
    // تغيير حالة الاتصال (متصل/غير متصل)
    Route::post('/toggle-online', [MessengerController::class, 'toggleOnlineStatus'])->name('toggle-online');
    
    // تحديث موقع المندوب
    Route::post('/update-location', [MessengerController::class, 'updateLocation'])->name('update-location');
    
    // تغيير حالة المندوب (متاح، مشغول، إلخ)
    Route::post('/update-status', [MessengerController::class, 'updateStatus'])->name('update-status');
    
    // الملف الشخصي
    Route::get('/profile', [MessengerController::class, 'profile'])->name('profile');
    Route::post('/profile', [MessengerController::class, 'updateProfile'])->name('profile.update');
    
    // معلومات المركبة
    Route::get('/vehicle', [MessengerController::class, 'vehicle'])->name('vehicle');
    Route::post('/vehicle', [MessengerController::class, 'updateVehicle'])->name('vehicle.update');
    
    // الإحصائيات
    Route::get('/statistics', [MessengerController::class, 'statistics'])->name('statistics');
});
