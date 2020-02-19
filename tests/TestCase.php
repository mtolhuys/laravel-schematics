<?php

namespace Mtolhuys\LaravelSchematics\Tests;

use Mtolhuys\LaravelSchematics\LaravelSchematicsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public $modelNamespace;

    /**
     * @before
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->modelNamespace = config('schematics.model-namespace');
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
        $app['config']->set('schematics.model-namespace', 'App\\');
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

}
