<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\HealthApiController;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', [HealthApiController::class, 'check'])->name('api.v1.health');
});
