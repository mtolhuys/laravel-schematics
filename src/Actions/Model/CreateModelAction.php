<?php

namespace Mtolhuys\LaravelSchematics\Actions\Model;

use Illuminate\Support\Facades\File;

class CreateModelAction
{
    /**
     * @param $request
     * @return void
     */
    public function execute($request)
    {
        $name = $request['name'];
        $namespace = config('schematics.namespace');
        $stub = __DIR__ . '/../../../resources/stubs/model.stub';
        $path = app_path(str_replace(['App\\', '\\'], ['', '/'], $namespace) . "{$name}.php");

        File::put($path, str_replace(
            ['$namespace$', '$model$', '$fillables$'],
            [rtrim($namespace, '\\'), $name, $this->getFillables($request['fields'])],
            File::get($stub)
        ));
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
