<?php

use App\Http\Controllers\Api\User\Auth\AuthController as UserAuthController;
use App\Http\Controllers\Api\User\Auth\SocialAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| All public / authenticated user APIs live here.
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/logout', [UserAuthController::class, 'logout'])
        ->middleware(['auth:sanctum', 'ensure.user']);

    // OTP-based login
    Route::post('/otp/request', [UserAuthController::class, 'requestOtp']);
    Route::post('/otp/verify', [UserAuthController::class, 'verifyOtp']);

    // Social login (Google, Apple, Meta)
    Route::post('/social/{provider}', [SocialAuthController::class, 'handle']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum', 'ensure.user']);

