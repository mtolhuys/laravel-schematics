<?php

namespace Mtolhuys\LaravelSchematics;

use Illuminate\Support\ServiceProvider;
use Mtolhuys\LaravelSchematics\Console\Commands\CreateMigration;
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
                CreateMigration::class,
                CreateModel::class,
            ]);
        }

        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'schematics');

        $this->publishes([
            __DIR__.'/../config/schematics.php' => config_path('schematics.php'),
        ]);

        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/schematics'),
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
