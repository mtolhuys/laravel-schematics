<?php

namespace Mtolhuys\LaravelSchematics\Actions;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Services\RuleParser;

class CreateMigrationAction
{
    /**
     * @param $request
     * @return string
     */
    public function execute($request)
    {
        $model = ucfirst($request['name']);
        $table = Str::plural(Str::snake($model));
        $stub = __DIR__ . '/../../resources/stubs/migration.stub';
        $filename = 'database/migrations/' . date('Y_m_d_His') . "_create_{$table}_table.php";
        $columns = RuleParser::rulesToMigrationColumns($request['fields']);

        File::put(base_path($filename), str_replace(
            ['$classname$', '$table$', '$columns$'],
            ['Create' . Str::plural($model) . 'Table', $table, $columns],
            File::get($stub)
        ));
    }
}
