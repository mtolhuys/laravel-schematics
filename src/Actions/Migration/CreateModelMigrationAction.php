<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Services\RuleParser;
use Mtolhuys\LaravelSchematics\Actions\Migration\Traits\CreatesMigrations;

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

        if(! File::isDirectory(dirname(base_path($this->filename)))){
            File::makeDirectory(dirname(base_path($this->filename)), 0777, true, true);
        }

        File::put(base_path($this->filename), str_replace([
            '$classname$',
            '$table$',
            '$columns$'
        ], [
            'Create' . Str::plural($model) . 'Table',
            $table,
            rtrim($this->getColumns($request))
        ], File::get($stub)));
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
