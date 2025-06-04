<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Accounts\AccountsApiController;
use App\Http\Controllers\Api\V1\Accounts\AccountTransactionsApiController;
use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\Categories\CategoriesApiController;
use App\Http\Controllers\Api\V1\HealthApiController;
use App\Http\Controllers\Api\V1\Merchants\MerchantsApiController;
use App\Http\Controllers\Api\V1\Tags\TagsApiController;
use App\Http\Controllers\Api\V1\Teams\TeamAccountsApiController;
use App\Http\Controllers\Api\V1\Teams\TeamCategoriesApiController;
use App\Http\Controllers\Api\V1\Teams\TeamMerchantsApiController;
use App\Http\Controllers\Api\V1\Teams\TeamsApiController;
use App\Http\Controllers\Api\V1\Teams\TeamTagsApiController;
use App\Http\Controllers\Api\V1\Teams\TeamTransactionsApiController;
use App\Http\Controllers\Api\V1\Teams\TeamUsersApiController;
use App\Http\Controllers\Api\V1\Transactions\TransactionsApiController;
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

        Route::get('teams/{team}/relationships/accounts', [TeamAccountsApiController::class, 'accountsRelationships'])
            ->name('api.v1.teams.relationships.accounts');
        Route::get('teams/{team}/accounts', [TeamAccountsApiController::class, 'accounts'])
            ->name('api.v1.teams.accounts');

        Route::get('teams/{team}/relationships/merchants', [
            TeamMerchantsApiController::class, 'merchantsRelationships',
        ])
            ->name('api.v1.teams.relationships.merchants');
        Route::get('teams/{team}/merchants', [TeamMerchantsApiController::class, 'merchants'])
            ->name('api.v1.teams.merchants');

        Route::get('teams/{team}/relationships/categories', [
            TeamCategoriesApiController::class, 'categoriesRelationships',
        ])
            ->name('api.v1.teams.relationships.categories');
        Route::get('teams/{team}/categories', [TeamCategoriesApiController::class, 'categories'])
            ->name('api.v1.teams.categories');

        Route::get('teams/{team}/relationships/tags', [TeamTagsApiController::class, 'tagsRelationships'])
            ->name('api.v1.teams.relationships.tags');
        Route::get('teams/{team}/tags', [TeamTagsApiController::class, 'tags'])
            ->name('api.v1.teams.tags');

        Route::get('teams/{team}/relationships/transactions', [
            TeamTransactionsApiController::class, 'transactionsRelationships',
        ])
            ->name('api.v1.teams.relationships.transactions');
        Route::get('teams/{team}/transactions', [TeamTransactionsApiController::class, 'transactions'])
            ->name('api.v1.teams.transactions');

        // Accounts
        Route::apiResource('accounts', AccountsApiController::class)->names('api.v1.accounts')->except(['update']);
        Route::put('accounts/{account}', [AccountsApiController::class, 'replace'])
            ->name('api.v1.accounts.replace');
        Route::patch('accounts/{account}', [AccountsApiController::class, 'update'])
            ->name('api.v1.accounts.update');

        Route::get('accounts/{account}/relationships/transactions', [
            AccountTransactionsApiController::class, 'transactionsRelationships',
        ])
            ->name('api.v1.accounts.relationships.transactions');
        Route::get('accounts/{account}/transactions', [AccountTransactionsApiController::class, 'transactions'])
            ->name('api.v1.accounts.transactions');

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

        // Tags
        Route::apiResource('tags', TagsApiController::class)->names('api.v1.tags')->except(['update']);
        Route::put('tags/{tag}', [TagsApiController::class, 'replace'])
            ->name('api.v1.tags.replace');
        Route::patch('tags/{tag}', [TagsApiController::class, 'update'])
            ->name('api.v1.tags.update');

        // Transactions
        Route::apiResource('transactions', TransactionsApiController::class)
            ->names('api.v1.transactions')
            ->except(['update']);
        Route::put('transactions/{transaction}', [TransactionsApiController::class, 'replace'])
            ->name('api.v1.transactions.replace');
        Route::patch('transactions/{transaction}', [TransactionsApiController::class, 'update'])
            ->name('api.v1.transactions.update');
    });
});
