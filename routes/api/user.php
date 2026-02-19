<?php

use App\Http\Controllers\Api\User\Auth\AuthController as UserAuthController;
use App\Http\Controllers\Api\User\Auth\SocialAuthController;
use App\Http\Controllers\Api\User\Order\UserOrderController;
use App\Http\Controllers\Api\User\Package\UserPackageController;
use App\Http\Controllers\Api\User\Test\UserTestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| USER AUTH ROUTES (PUBLIC)
|--------------------------------------------------------------------------
| - Register
| - Login
| - Logout
| - OTP Login
| - Social Login
|--------------------------------------------------------------------------
*/
Route::prefix('user/auth')->group(function () {

    // Register new user
    Route::post('register', [UserAuthController::class, 'register']);

    // Login with email/password
    Route::post('login', [UserAuthController::class, 'login']);

    // Logout (Authenticated)
    Route::post('logout', [UserAuthController::class, 'logout'])
        ->middleware(['auth:sanctum', 'ensure.user']);

    /*
    |--------------------------------------------------------------------------
    | OTP AUTHENTICATION
    |--------------------------------------------------------------------------
    */
    Route::post('otp/request', [UserAuthController::class, 'requestOtp']);
    Route::post('otp/verify', [UserAuthController::class, 'verifyOtp']);

    /*
    |--------------------------------------------------------------------------
    | SOCIAL AUTHENTICATION
    |--------------------------------------------------------------------------
    */
    Route::post('social/{provider}', [SocialAuthController::class, 'handle']);
});


/*
|--------------------------------------------------------------------------
| USER PROTECTED ROUTES
|--------------------------------------------------------------------------
| Middleware:
| - auth:sanctum
| - ensure.user
|--------------------------------------------------------------------------
*/
Route::prefix('user')->middleware(['auth:sanctum', 'ensure.user'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH CHECK
    |--------------------------------------------------------------------------
    */
    Route::get('init', function () {
        return response()->json([
            'message' => 'You Authenticate User.'
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | PACKAGE MODULE
    |--------------------------------------------------------------------------
    | - List available packages
    | - View package details
    | - Buy package
    |--------------------------------------------------------------------------
    */
    Route::prefix('packages')->group(function () {

        // Fetch all active packages
        Route::get('/', [UserPackageController::class, 'index']);

        // Fetch single package detail
        Route::get('{id}', [UserPackageController::class, 'show']);

        // Buy package (create order)
        Route::post('{id}/buy', [UserOrderController::class, 'buy']);
    });

    /*
    |--------------------------------------------------------------------------
    | ORDER MODULE
    |--------------------------------------------------------------------------
    | - List user orders
    | - View order details
    |--------------------------------------------------------------------------
    */
    Route::prefix('orders')->group(function () {

        // My Orders
        Route::get('/', [UserOrderController::class, 'index']);
        // Single Order
        Route::get('{id}', [UserOrderController::class, 'show']);

        // Create Razorpay order
        Route::post('{orderId}/pay', [UserOrderController::class, 'pay']);

        // Verify payment
        Route::post('{orderId}/verify', [UserOrderController::class, 'verify']);
    });

    Route::prefix('tests')->group(function () {

        // List tests of active package
        Route::get('/', [UserTestController::class, 'index']);

    });

    /*
    |--------------------------------------------------------------------------
    | FUTURE MODULES (Coming Next)
    |--------------------------------------------------------------------------
    | - My Active Packages
    | - Test Start / Submit
    | - Result
    | - Notifications
    |--------------------------------------------------------------------------
    */
});
