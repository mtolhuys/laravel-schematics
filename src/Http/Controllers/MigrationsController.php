<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Mtolhuys\LaravelSchematics\Actions\Migration\DeleteLastMigrationAction;

class MigrationsController extends Controller
{
    public function run()
    {
        Artisan::call('migrate');

        Cache::forget('schematics');

        return Artisan::output();
    }

    public function rollback()
    {
        Artisan::call('migrate:rollback');

        Cache::forget('schematics');

        return Artisan::output();
    }

    public function seed()
    {
        Artisan::call('db:seed');

        Cache::forget('schematics');

        return Artisan::output();
    }

    public function refresh()
    {
        Artisan::call('migrate:refresh', [
            '--force' => true,
        ]);

        Cache::forget('schematics');

        return Artisan::output();
    }

    public function fresh()
    {
        Artisan::call('migrate:fresh');

        Cache::forget('schematics');

        return Artisan::output();
    }

    public function deleteLast()
    {
        (new DeleteLastMigrationAction())->execute();

        Cache::forget('schematics');

        return response('Last migration removed', 200);
    }
}
