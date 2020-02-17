<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Actions\Migration\Traits\CreatesMigrations;

class CreateRelationMigrationAction
{
    use CreatesMigrations;

    /**
     * @param $request
     * @return void
     */
    public function execute($request)
    {
        $source = app($request['source'])->getTable();
        $target = app($request['target'])->getTable();
        $foreignKey = $this->getForeignKey($request);
        $stub = __DIR__ . '/../../../resources/stubs/migration/relation.stub';
        $this->filename = 'database/migrations/'
            . date('Y_m_d_His')
            . "_create_{$source}_{$target}_relation.php";

        File::put(base_path($this->filename), str_replace([
            '$source$',
            '$target$',
            '$classname$',
            '$column$',
            '$foreignKey$'
        ], [
            $source,
            $target,
            'Create' . ucfirst(Str::camel($source)) . ucfirst(Str::camel($target)) . 'Relation',
            "\$table->foreign('$foreignKey')->references('{$this->getLocalKey($request)}')->on('$target');",
            $foreignKey
        ], File::get($stub)));
    }
}
