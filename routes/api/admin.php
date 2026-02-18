<?php

use App\Http\Controllers\Api\Admin\Auth\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\RolePermission\RolePermissionController;
use App\Http\Middleware\Permissions\CheckPermission;
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

Route::get('init', function () {
    return response()->json(['message' => 'Api Running.',]);
});

Route::prefix('admin/auth')->group(function () {
    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware(['auth:sanctum', 'ensure.admin']);

    // Two-step login (2FA) for admins via OTP
    Route::post('/two-factor/start', [AdminAuthController::class, 'startTwoFactor']);
    Route::post('/two-factor/verify', [AdminAuthController::class, 'verifyTwoFactor']);
});


/*
|--------------------------------------------------------------------------
| Admin Protected APIs
|--------------------------------------------------------------------------
| auth:sanctum + ensure.admin applied once
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth:sanctum', 'ensure.admin'])->group(function () {
    // test api
    Route::get('init', function () {
        return response()->json(['message' => 'You Authenticate User.',]);
    })->middleware(CheckPermission::class);

    Route::prefix('access')->group(function () {
        /* ---------- Roles CRUD ---------- */
        Route::get('roles', [RolePermissionController::class, 'roles']);        // list roles
        Route::post('roles', [RolePermissionController::class, 'createRole']);  // create role
        Route::get('roles/{id}', [RolePermissionController::class, 'roleDetails']); // role detail (edit)
        Route::put('roles/{id}', [RolePermissionController::class, 'updateRole']);  // update role
        Route::delete('roles/{id}', [RolePermissionController::class, 'deleteRole']);  // delete role

        /* ---------- Permissions ---------- */
        Route::get('permissions', [RolePermissionController::class, 'permissions']); // all permissions (grouped)

        /* ---------- Assign Permissions to Role ---------- */
        Route::post('roles/{id}/permissions', [RolePermissionController::class, 'assignPermissions']);
    });

    Route::get('me', function (Request $request) {
        return $request->user();
    })->middleware(CheckPermission::class . ':package.create,package.update');

});
