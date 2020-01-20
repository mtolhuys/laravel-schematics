<?php

namespace Mtolhuys\LaravelSchematics\Actions;

use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Services\ColumnParser;

class GenerateModel
{
    public function __invoke(string $table, array $rules): void
    {
        $model = ucfirst($table);
        $filename = "app/Models/$model.php";
        $fillables = ColumnParser::rulesToFillables($rules);

        File::put(base_path($filename), str_replace(
            ['$model$', '$fillables$'],
            [$model, $fillables],
            File::get(__DIR__ . '/stubs/model.stub')
        ));
    }
}
