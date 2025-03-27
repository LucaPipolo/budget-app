<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\HealthApiController;
use App\Http\Controllers\Api\V1\Teams\TeamsApiController;
use App\Http\Controllers\Api\V1\Teams\TeamUsersApiController;

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

        // Teams
        Route::apiResource('teams', TeamsApiController::class)->names('api.v1.teams')->except(['update']);
        Route::put('teams/{team}', [TeamsApiController::class, 'replace'])->name('api.v1.teams.replace');

        Route::get('teams/{team}/relationships/users', [TeamUsersApiController::class, 'usersRelationships'])
            ->name('api.v1.teams.relationships.users');
        Route::get('teams/{team}/users', [TeamUsersApiController::class, 'users'])
            ->name('api.v1.teams.users');
    });
});
