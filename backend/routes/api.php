<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\VoiceCallController;

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-location', [AuthController::class, 'verifyLocation']);

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Messaging Routes
    Route::post('/messages/send', [MessageController::class, 'sendMessage']);
    Route::get('/messages/{receiverId}', [MessageController::class, 'getMessages']);
    Route::delete('/messages/{messageId}', [MessageController::class, 'deleteMessage']);

    // Voice Call Routes
    Route::post('/calls/initiate', [VoiceCallController::class, 'initiateCall']);
    Route::post('/calls/{callId}/end', [VoiceCallController::class, 'endCall']);
    Route::get('/calls/history', [VoiceCallController::class, 'getCallHistory']);
});
