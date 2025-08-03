<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Sentry\Laravel\Integration;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 僅當存在 SENTRY_DSN 時初始化 Sentry
        if (env('SENTRY_DSN')) {
            Integration::init([
                'dsn' => env('SENTRY_DSN'),
                'traces_sample_rate' => 1.0,
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
