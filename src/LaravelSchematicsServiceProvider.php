<?php

namespace Mtolhuys\LaravelSchematics;

use Illuminate\Support\ServiceProvider;
use Mtolhuys\LaravelSchematics\Console\Commands\install;
use Mtolhuys\LaravelSchematics\Console\Commands\CreateModel;

class LaravelSchematicsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                install::class,
            ]);
        }

        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'schematics');

        $this->publishes([
            __DIR__.'/../config/schematics.php' => config_path('schematics.php'),
        ]);

        $this->publishes([
            __DIR__.'/../dist/app.js' => public_path('vendor/schematics/app.js'),
            __DIR__.'/../resources/images/plumb-arrows' => public_path('vendor/schematics/images'),
        ], 'public');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/schematics.php', 'schematics');
    }
}
