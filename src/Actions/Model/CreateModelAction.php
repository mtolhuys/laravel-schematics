<?php

namespace Mtolhuys\LaravelSchematics\Actions\Model;

use Mtolhuys\LaravelSchematics\Services\StubWriter;

class CreateModelAction
{
    /**
     * @param $request
     * @return void
     */
    public function execute($request)
    {
        $name = $request['name'];
        $stub = __DIR__ . '/../../../resources/stubs/model.stub';
        $file = config('schematics.model.path') . "/{$name}.php";

        (new StubWriter($file, $stub))->write([
            '$fillables$' => $this->getFillables($request['fields']),
            '$namespace$' => rtrim(config('schematics.model.namespace'), '\\'),
            '$model$' => $name,
        ]);
    }

    /**
     * @param array $fields
     * @return string
     */
    private function getFillables(array $fields): string
    {
        $fillables = '';

        foreach ($fields as $index => $field) {
            if ($index === 0) {
                $fillables .= "'{$field['name']}',";
            } else {
                $fillables .= PHP_EOL . str_repeat(' ', 8) . "'{$field['name']}',";
            }
        }

        return $fillables;
    }
}
