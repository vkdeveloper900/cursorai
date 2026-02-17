<?php

use App\Http\Controllers\Api\Admin\Auth\AuthController as AdminAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| All admin-specific APIs live here.
|
*/

Route::prefix('admin/auth')->group(function () {
    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->middleware(['auth:sanctum', 'ensure.admin']);

    // Two-step login (2FA) for admins via OTP
    Route::post('/two-factor/start', [AdminAuthController::class, 'startTwoFactor']);
    Route::post('/two-factor/verify', [AdminAuthController::class, 'verifyTwoFactor']);
});

Route::get('/admin/me', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum', 'ensure.admin']);

