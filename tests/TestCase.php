<?php

namespace Mtolhuys\LaravelSchematics\Tests;

use Mtolhuys\LaravelSchematics\LaravelSchematicsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public
        $modelNamespace,
        $controllerNamespace,
        $formRequestNamespace;

    /**
     * @before
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->modelNamespace = config('schematics.model.namespace');
        $this->controllerNamespace = config('schematics.controller-namespace');
        $this->formRequestNamespace = config('schematics.form-request-namespace');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelSchematicsServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('schematics.model.namespace', 'App\\');
        $app['config']->set('schematics.model.path', app_path());
        $app['config']->set('schematics.model.paths', [
            app_path(),
            base_path('src'),
        ]);
        $app['config']->set('schematics.controller-namespace', null);
        $app['config']->set('schematics.form-request-namespace', 'App\\Http\\Requests');
    }

}
