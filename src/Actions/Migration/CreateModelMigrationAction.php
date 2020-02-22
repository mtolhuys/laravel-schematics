<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Services\RuleParser;
use Mtolhuys\LaravelSchematics\Actions\Migration\Traits\CreatesMigrations;
use Mtolhuys\LaravelSchematics\Services\StubWriter;

class CreateModelMigrationAction
{
    use CreatesMigrations;

    /**
     * @param $request
     */
    public function execute($request)
    {
        $model = ucfirst($request['name']);
        $table = Str::plural(Str::snake($model));
        $stub = __DIR__ . '/../../../resources/stubs/migration/model.stub';
        $this->filename = 'database/migrations/'
            . date('Y_m_d_His')
            . "_create_{$table}_table.php";

        (new StubWriter(base_path($this->filename), $stub))->write([
            '$classname$' => 'Create' . Str::plural($model) . 'Table',
            '$columns$' => rtrim($this->getColumns($request)),
            '$table$' => $table,
        ]);
    }

    /**
     * @param $request
     * @return string
     */
    private function getColumns($request): string
    {
        $columns = RuleParser::fieldsToMigrationMethods(
            $this->getFields($request['fields'])
        );

        if (json_decode($request['options']['hasTimestamps'], false)) {
            $columns .= '$table->timestamps();';
        }

        return $columns;
    }
}
