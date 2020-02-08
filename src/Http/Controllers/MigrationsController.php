<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Routing\Controller;

class MigrationsController extends Controller
{
    public function run()
    {
        Artisan::call('migrate');

        return Artisan::output();
    }

    public function rollback()
    {
        Artisan::call('migrate:rollback');

        return Artisan::output();
    }

    public function seed()
    {
        Artisan::call('db:seed');

        return Artisan::output();
    }

    public function refresh()
    {
        Artisan::call('migrate:refresh', [
            '--force' => true,
        ]);

        return Artisan::output();
    }
}
