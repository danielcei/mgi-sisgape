<?php

namespace App\Providers;

use App\Services\SpcServices;
use Illuminate\Support\ServiceProvider;

class SpcConsultaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SpcServices::class, function ($app) {
            return new SpcServices();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
