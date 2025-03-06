<?php

use Illuminate\Support\Facades\Route;

// Authentication Controllers
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordResetController;

// Main Application Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\AdminController;

// Location and Geolocation
use App\Http\Controllers\LocationController;

// Root and Landing Routes
Route::get('/', [HomeController::class, 'landing'])->name('home');

// About page route
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Contact page route
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

// Features page route
Route::get('/features', [HomeController::class, 'features'])->name('features');

// App overview route
Route::get('/app', [HomeController::class, 'appOverview'])->name('app');

// Testimonials page route
Route::get('/testimonials', [HomeController::class, 'testimonials'])->name('testimonials');

// Privacy policy page route
Route::get('/privacy', [HomeController::class, 'privacyPolicy'])->name('privacy');

// Terms of service page route
Route::get('/terms', [HomeController::class, 'termsOfService'])->name('terms');

// Cookies policy page route
Route::get('/cookies', [HomeController::class, 'cookiesPolicy'])->name('cookies');

// FAQ page route
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

// Authentication Routes
Route::prefix('auth')->group(function () {
    // Login Routes
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    
    // Registration Routes
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    
    // Password Reset Routes
    Route::get('password/reset', [PasswordResetController::class, 'showResetForm'])->name('password.request');
    Route::post('password/email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [PasswordResetController::class, 'reset'])->name('password.update');
    
    // Email Verification Routes
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});

// Location Routes
Route::prefix('location')->group(function () {
    Route::post('validate', [LocationController::class, 'validateLocation'])->name('location.validate');
    Route::get('supported', [LocationController::class, 'getSupportedLocations'])->name('location.supported');
});

// Authenticated Routes (Require Authentication)
Route::middleware(['auth', 'verified'])->group(function () {
    // Home Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/verify', [ProfileController::class, 'verifyIdentity'])->name('profile.verify');
    });

    // Messaging Routes
    Route::prefix('messaging')->group(function () {
        Route::get('/', [MessagingController::class, 'index'])->name('messaging.index');
        Route::get('/conversations', [MessagingController::class, 'conversations'])->name('messaging.conversations');
        Route::get('/conversation/{id}', [MessagingController::class, 'conversation'])->name('messaging.conversation');
        Route::post('/send', [MessagingController::class, 'sendMessage'])->name('messaging.send');
        Route::delete('/message/{id}', [MessagingController::class, 'deleteMessage'])->name('messaging.delete');
    });

    // Call Routes
    Route::prefix('calls')->group(function () {
        Route::get('/', [CallController::class, 'index'])->name('calls.index');
        Route::post('/initiate', [CallController::class, 'initiateCall'])->name('calls.initiate');
        Route::post('/accept/{callId}', [CallController::class, 'acceptCall'])->name('calls.accept');
        Route::post('/end/{callId}', [CallController::class, 'endCall'])->name('calls.end');
    });

    // Admin Routes (Additional Admin Middleware)
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'userManagement'])->name('admin.users');
        Route::get('/locations', [AdminController::class, 'locationManagement'])->name('admin.locations');
        Route::post('/user/{id}/status', [AdminController::class, 'updateUserStatus'])->name('admin.user.status');
    });
});

// Fallback Route for undefined routes
Route::fallback(function () {
    return view('errors.default', [
        'message' => 'الصفحة غير موجودة'
    ]);
});
