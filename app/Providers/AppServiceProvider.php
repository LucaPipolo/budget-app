<?php

declare(strict_types=1);

namespace App\Providers;

use App\Exceptions\TooManyRequestsException;
use App\Models\Transaction;
use App\Observers\TransactionObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Preserve brace position.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Disable Laravel mass assignment protection since Filament only saves valid data to models.
        // @see https://filamentphp.com/docs/3.x/panels/getting-started#unguarding-all-models
        Model::unguard();

        Password::defaults(function () {
            return Password::min(13)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });

        RateLimiter::for('api_login', function (Request $request) {
            return Limit::perMinute(4)->by(
                $request->user()?->id ?: $request->ip()
            )->response(function (): JsonResponse {
                throw new TooManyRequestsException();
            });
        });

        Transaction::observe(TransactionObserver::class);
    }
}
