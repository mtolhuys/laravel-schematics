<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Services\RuleParser;
use Mtolhuys\LaravelSchematics\Http\Requests\CreateRelationRequest;

class CreateMigrationAction
{
    /**
     * @param $request
     * @return bool|int
     */
    public function execute($request)
    {
        if ($request instanceof CreateRelationRequest) {
            return $this->createRelationMigration($request);
        }

        return $this->createModelMigration($request);
    }

    /**
     * @param $request
     * @return bool|int
     */
    private function createRelationMigration($request)
    {
        $source = app($request['source'])->getTable();
        $target = app($request['target'])->getTable();
        $foreignKey = $this->getForeignKey($request);
        $stub = __DIR__ . '/../../../resources/stubs/migration/relation.stub';
        $filename = 'database/migrations/'
            . date('Y_m_d_His')
            . "_create_{$source}_{$target}_relation.php";

        return File::put(base_path($filename), str_replace([
            '$source$',
            '$target$',
            '$classname$',
            '$column$',
            '$foreignKey$'
        ], [
            $source,
            $target,
            'Create' . ucfirst($source) . ucfirst($target) . 'Relation',
            "\$table->foreign('$foreignKey')->references('{$this->getLocalKey($request)}')->on('$target');",
            $foreignKey
        ], File::get($stub)));
    }

    /**
     * @param $request
     * @return bool|int
     */
    private function createModelMigration($request)
    {
        $model = ucfirst($request['name']);
        $table = Str::plural(Str::snake($model));
        $stub = __DIR__ . '/../../../resources/stubs/migration/model.stub';
        $filename = 'database/migrations/'
            . date('Y_m_d_His')
            . "_create_{$table}_table.php";

        return File::put(base_path($filename), str_replace([
            '$classname$',
            '$table$',
            '$columns$'
        ], [
            'Create' . Str::plural($model) . 'Table',
            $table,
            RuleParser::rulesToMigrationColumns($request['fields'])
        ], File::get($stub)));
    }

    /**
     * @param $request
     * @return string
     */
    private function getLocalKey($request): string
    {
        return $request['method']['localKey'] ?? 'id';
    }

    /**
     * @param $request
     * @return string
     */
    private function getForeignKey($request): string
    {
        return $request['method']['foreignKey']
            ?? strtolower(substr(strrchr($request['target'], "\\"), 1) . '_id');
    }
}
