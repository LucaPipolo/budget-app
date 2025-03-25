<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\HealthApiController;

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
        Route::get('/auth/me', [AuthApiController::class, 'me'])->name('api.v1.auth.me');
        Route::post('/auth/logout', [AuthApiController::class, 'logout'])->name('api.v1.auth.logout');
        Route::post('/auth/refresh-token', [AuthApiController::class, 'refreshToken'])
            ->middleware('throttle:api_login')
            ->name('api.v1.auth.refresh-token');
    });
});
