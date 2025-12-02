<?php

namespace DenizGolbas\LaravelAutoRelationalResources;

use Illuminate\Support\ServiceProvider;

class AutoRelationalResourcesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/auto-relational-resources.php',
            'auto-relational-resources'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/auto-relational-resources.php' => config_path('auto-relational-resources.php'),
        ], 'config');
    }
}

