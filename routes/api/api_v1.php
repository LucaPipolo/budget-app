<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Accounts\AccountsApiController;
use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\Categories\CategoriesApiController;
use App\Http\Controllers\Api\V1\HealthApiController;
use App\Http\Controllers\Api\V1\Merchants\MerchantsApiController;
use App\Http\Controllers\Api\V1\Teams\TeamsApiController;
use App\Http\Controllers\Api\V1\Teams\TeamUsersApiController;
use App\Http\Controllers\Api\V1\Uploads\UploadApiController;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', [HealthApiController::class, 'check'])->name('api.v1.health');

    Route::post('/auth/login', [AuthApiController::class, 'login'])
        ->middleware('throttle:api_login')
        ->name('api.v1.auth.login');

    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function (): void {
        // Auth
        Route::get('/auth/me', [AuthApiController::class, 'me'])->name('api.v1.auth.me');
        Route::post('/auth/logout', [AuthApiController::class, 'logout'])->name('api.v1.auth.logout');
        Route::post('/auth/refresh-token', [AuthApiController::class, 'refreshToken'])
            ->middleware('throttle:api_login')
            ->name('api.v1.auth.refresh-token');

        // Uploads
        Route::post('/uploads', [UploadApiController::class, 'store'])->name('api.v1.uploads.store');

        // Teams
        Route::apiResource('teams', TeamsApiController::class)->names('api.v1.teams')->except(['update']);
        Route::put('teams/{team}', [TeamsApiController::class, 'replace'])->name('api.v1.teams.replace');

        Route::get('teams/{team}/relationships/users', [TeamUsersApiController::class, 'usersRelationships'])
            ->name('api.v1.teams.relationships.users');
        Route::get('teams/{team}/users', [TeamUsersApiController::class, 'users'])
            ->name('api.v1.teams.users');

        // Accounts
        Route::apiResource('accounts', AccountsApiController::class)->names('api.v1.accounts')->except(['update']);
        Route::put('accounts/{account}', [AccountsApiController::class, 'replace'])
            ->name('api.v1.accounts.replace');
        Route::patch('accounts/{account}', [AccountsApiController::class, 'update'])
            ->name('api.v1.accounts.update');

        // Merchants
        Route::apiResource('merchants', MerchantsApiController::class)->names('api.v1.merchants')->except(['update']);
        Route::put('merchants/{merchant}', [MerchantsApiController::class, 'replace'])
            ->name('api.v1.merchants.replace');
        Route::patch('merchants/{merchant}', [MerchantsApiController::class, 'update'])
            ->name('api.v1.merchants.update');

        // Categories
        Route::apiResource('categories', CategoriesApiController::class)
            ->names('api.v1.categories')
            ->except(['update']);
        Route::put('categories/{category}', [CategoriesApiController::class, 'replace'])
            ->name('api.v1.categories.replace');
        Route::patch('categories/{category}', [CategoriesApiController::class, 'update'])
            ->name('api.v1.categories.update');
    });
});
