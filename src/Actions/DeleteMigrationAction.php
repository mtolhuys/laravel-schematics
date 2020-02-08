<?php

namespace Mtolhuys\LaravelSchematics\Actions;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Services\RuleParser;

class DeleteMigrationAction
{
    /**
     * @param $request
     * @return string
     */
    public function execute($request)
    {
        $table = Str::plural(Str::snake(
            substr($request['name'], strrpos($request['name'], '\\' )+1)
        ));
        $migrations = scandir(base_path('database/migrations/'));

        foreach ($migrations as $migration) {
            if (strpos($migration, $table) !== false) {
                File::delete(base_path("database/migrations/$migration"));
            }
        }
    }
}
