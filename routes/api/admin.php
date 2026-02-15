<?php

use App\Http\Controllers\Api\Admin\Auth\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\Meta\MetaController;
use App\Http\Controllers\Api\Admin\Meta\QuestionImportExportController;
use App\Http\Controllers\Api\Admin\Package\PackageController;
use App\Http\Controllers\Api\Admin\RolePermission\RolePermissionController;
use App\Http\Controllers\Api\Admin\Test\Question\QuestionController;
use App\Http\Controllers\Api\Admin\Test\Section\SectionController;
use App\Http\Controllers\Api\Admin\Test\TestController;
use App\Http\Controllers\Api\Admin\Test\TestSection\TestSectionController;
use App\Http\Controllers\Api\User\Test\TestQuestionController;
use App\Http\Middleware\Permissions\CheckPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Health Check
|--------------------------------------------------------------------------
*/
Route::get('init', function () {
    return response()->json(['message' => 'Api Running.']);
});
Route::get('questions/sample', [QuestionImportExportController::class, 'downloadSample']);

/*
|--------------------------------------------------------------------------
| ADMIN AUTH ROUTES (PUBLIC)
|--------------------------------------------------------------------------
| Login / Register / 2FA
|--------------------------------------------------------------------------
*/
Route::prefix('admin/auth')->group(function () {
    Route::post('register', [AdminAuthController::class, 'register']);
    Route::post('login', [AdminAuthController::class, 'login']);

    Route::post('logout', [AdminAuthController::class, 'logout'])
        ->middleware(['auth:sanctum', 'ensure.admin']);

    // Two Factor Authentication
    Route::post('two-factor/start', [AdminAuthController::class, 'startTwoFactor']);
    Route::post('two-factor/verify', [AdminAuthController::class, 'verifyTwoFactor']);
});

/*
|--------------------------------------------------------------------------
| ADMIN PROTECTED ROUTES
|--------------------------------------------------------------------------
| auth:sanctum + ensure.admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth:sanctum', 'ensure.admin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH CHECK / INIT
    |--------------------------------------------------------------------------
    */
    Route::get('init', function () {
        return response()->json(['message' => 'You Authenticate User.']);
    })->middleware(CheckPermission::class);

    /*
    |--------------------------------------------------------------------------
    | META ROUTES (DROPDOWN / CONSTANT DATA)
    |--------------------------------------------------------------------------
    */
    Route::prefix('meta')->group(function () {
        Route::get('difficulties', [MetaController::class, 'difficulties']);
        Route::get('question-types', [MetaController::class, 'questionTypes']);
        Route::get('test-statuses', [MetaController::class, 'testStatuses']);
        Route::get('yes-no', [MetaController::class, 'yesNo']);
    });

    /*
    |--------------------------------------------------------------------------
    | TEST MODULE (MASTER DATA)
    |--------------------------------------------------------------------------
    | Sections & Questions
    |--------------------------------------------------------------------------
    */
    Route::prefix('test')->group(function () {

        /*
        |----------------------------------
        | SECTION MASTER (CRUD)
        |----------------------------------
        */
        Route::prefix('sections')->group(function () {
            Route::get('/', [SectionController::class, 'index']);
            Route::post('/', [SectionController::class, 'store']);
            Route::get('{id}', [SectionController::class, 'show']);
            Route::put('{id}', [SectionController::class, 'update']);
            Route::delete('{id}', [SectionController::class, 'destroy']);
        });

        /*
        |----------------------------------
        | QUESTION BANK (CRUD)
        |----------------------------------
        */
        Route::prefix('questions')->group(function () {
            Route::get('/', [QuestionController::class, 'index']);
            Route::post('/', [QuestionController::class, 'store']);
            Route::get('{id}', [QuestionController::class, 'show']);
            Route::put('{id}', [QuestionController::class, 'update']);
            Route::delete('{id}', [QuestionController::class, 'destroy']);
        });


        /*
         |----------------------------------
         | TEST MASTER (CRUD)
         |----------------------------------
         | /admin/test/tests
         */
        Route::prefix('tests')->group(function () {
            Route::get('/', [TestController::class, 'index']);
            Route::post('/', [TestController::class, 'store']);
            Route::get('{id}', [TestController::class, 'show']);
            Route::put('{id}', [TestController::class, 'update']);
            Route::delete('{id}', [TestController::class, 'destroy']);

            // Publish Test
            Route::post('{id}/publish', [TestController::class, 'publish']);
        });

        Route::prefix('tests/{testId}')->group(function () {
            Route::get('sections', [TestSectionController::class, 'index']);
            Route::post('sections', [TestSectionController::class, 'store']);
            Route::delete('sections/{id}', [TestSectionController::class, 'destroy']);

            Route::get('debug-generate', [TestController::class, 'debugGenerateQuestions']);

            Route::get('start', [TestQuestionController::class, 'startTest']);
        });

        Route::prefix('import')->group(function () {
            Route::get('questions/sample', [QuestionImportExportController::class, 'downloadSample']);
            Route::post('questions', [QuestionImportExportController::class, 'import']);
        });
    });


    Route::prefix('packages')->group(function () {

        Route::get('/', [PackageController::class, 'index']);          // list
        Route::post('/', [PackageController::class, 'store']);         // create
        Route::get('{id}', [PackageController::class, 'show']);        // detail
        Route::put('{id}', [PackageController::class, 'update']);      // update
        Route::delete('{id}', [PackageController::class, 'destroy']);  // delete
    });

    /*
    |--------------------------------------------------------------------------
    | ACCESS CONTROL (ROLES & PERMISSIONS)
    |--------------------------------------------------------------------------
    */
    Route::prefix('access')->group(function () {

        // Roles
        Route::get('roles', [RolePermissionController::class, 'roles']);
        Route::post('roles', [RolePermissionController::class, 'createRole']);
        Route::get('roles/{id}', [RolePermissionController::class, 'roleDetails']);
        Route::put('roles/{id}', [RolePermissionController::class, 'updateRole']);
        Route::delete('roles/{id}', [RolePermissionController::class, 'deleteRole']);

        // Permissions
        Route::get('permissions', [RolePermissionController::class, 'permissions']);

        // Assign permissions to role
        Route::post('roles/{id}/permissions',
            [RolePermissionController::class, 'assignPermissions']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED ADMIN PROFILE
    |--------------------------------------------------------------------------
    */
    Route::get('me', function (Request $request) {
        return $request->user();
    })->middleware(CheckPermission::class . ':package.create,package.update');

});
