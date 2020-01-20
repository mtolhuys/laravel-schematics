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
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateMigration::class,
                CreateModel::class,
            ]);
        }

        $this->loadHelpers();

        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'schematics');

        $this->publishes([
            __DIR__.'/../config/schematics.php' => config_path('schematics.php'),
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/schematics.php', 'schematics');
    }

    /**
     * Loading helpers file
     */
    public function loadHelpers(): void
    {
        require_once __DIR__ . '/helpers.php';
    }
}
