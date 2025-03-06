<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SupervisorController;
use App\Http\Controllers\Auth\SupervisorAuthController;

/*
|--------------------------------------------------------------------------
| مسارات المشرفين
|--------------------------------------------------------------------------
|
| هنا يمكنك تسجيل مسارات المشرفين للتطبيق. هذه المسارات يتم تحميلها
| من قبل RouteServiceProvider داخل مجموعة تحوي وسيط "web".
|
*/

// صفحة استكمال بيانات المشرف
Route::get('/complete-profile', [SupervisorAuthController::class, 'completeProfile'])->name('complete-profile');
Route::post('/store-profile', [SupervisorAuthController::class, 'storeProfile'])->name('store-profile');

// لوحة التحكم الرئيسية للمشرف
Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');

// إدارة المناديب
Route::get('/messengers', [SupervisorController::class, 'listMessengers'])->name('messengers.index');
Route::get('/messengers/create', [SupervisorController::class, 'createMessenger'])->name('messengers.create');
Route::post('/messengers', [SupervisorController::class, 'storeMessenger'])->name('messengers.store');
Route::get('/messengers/{id}', [SupervisorController::class, 'showMessenger'])->name('messengers.show');
Route::get('/messengers/{id}/edit', [SupervisorController::class, 'editMessenger'])->name('messengers.edit');
Route::put('/messengers/{id}', [SupervisorController::class, 'updateMessenger'])->name('messengers.update');
Route::delete('/messengers/{id}', [SupervisorController::class, 'destroyMessenger'])->name('messengers.destroy');

// تتبع المناديب على الخريطة
Route::get('/messenger-tracking', [SupervisorController::class, 'trackMessengers'])->name('messenger-tracking');

// إدارة المناطق
Route::get('/zones', [SupervisorController::class, 'listZones'])->name('zones.index');
Route::get('/zones/create', [SupervisorController::class, 'createZone'])->name('zones.create');
Route::post('/zones', [SupervisorController::class, 'storeZone'])->name('zones.store');
Route::get('/zones/{id}/edit', [SupervisorController::class, 'editZone'])->name('zones.edit');
Route::put('/zones/{id}', [SupervisorController::class, 'updateZone'])->name('zones.update');
Route::delete('/zones/{id}', [SupervisorController::class, 'destroyZone'])->name('zones.destroy');

// تعيين المناديب للمناطق
Route::post('/zones/{zone_id}/assign-messengers', [SupervisorController::class, 'assignMessengersToZone'])->name('zones.assign-messengers');

// إدارة الطلبات
Route::get('/orders', [SupervisorController::class, 'listOrders'])->name('orders.index');
Route::get('/orders/{id}', [SupervisorController::class, 'showOrder'])->name('orders.show');
Route::post('/orders/{id}/assign-messenger', [SupervisorController::class, 'assignOrderToMessenger'])->name('orders.assign-messenger');
Route::put('/orders/{id}/status', [SupervisorController::class, 'updateOrderStatus'])->name('orders.update-status');

// طلبات دون مناديب
Route::get('/unassigned-orders', [SupervisorController::class, 'unassignedOrders'])->name('orders.unassigned');

// الإحصائيات والتقارير
Route::get('/statistics', [SupervisorController::class, 'statistics'])->name('statistics');
Route::get('/reports/messengers', [SupervisorController::class, 'messengerReports'])->name('reports.messengers');
Route::get('/reports/zones', [SupervisorController::class, 'zoneReports'])->name('reports.zones');
Route::get('/reports/orders', [SupervisorController::class, 'orderReports'])->name('reports.orders');

// الملف الشخصي
Route::get('/profile', [SupervisorAuthController::class, 'profile'])->name('profile');
Route::put('/profile/update', [SupervisorAuthController::class, 'updateProfile'])->name('profile.update');
