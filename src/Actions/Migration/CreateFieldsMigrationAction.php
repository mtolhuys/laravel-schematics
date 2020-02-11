<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Services\RuleParser;
use Mtolhuys\LaravelSchematics\Actions\Migration\Traits\CreatesMigrations;

class CreateFieldsMigrationAction
{
    use CreatesMigrations;

    /**
     * @param $request
     */
    public function execute($request)
    {
        $columns = RuleParser::rulesToMigrationColumns($this->getFields($request['new_fields']));

        dd('$columns', $columns);

        File::put(base_path($this->filename), str_replace([
            '$classname$',
            '$table$',
            '$columns$'
        ], [
            'Create' . Str::plural($model) . 'Table',
            $table,
            RuleParser::rulesToMigrationColumns($this->getFields($request['fields']))
        ], File::get($stub)));
    }
}
