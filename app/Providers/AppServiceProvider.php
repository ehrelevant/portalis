<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('pin_email', function (Request $request) {
            if (env('SEND_PIN_TO_EMAIL', false)) {
                return [
                    // set rate limiter to 30 requests per minute
                    Limit::perHour(1800)->by($request->ip()),
                    Limit::perMinute(30)->by($request->ip()),
                ];
            } else {
                return Limit::none();
            }
        });
    }
}
