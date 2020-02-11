<?php

namespace Mtolhuys\LaravelSchematics\Actions\Model;

use Illuminate\Support\Facades\File;
use ReflectionClass;

class CreateFieldsAction
{
    /**
     * @param $request
     * @return void
     * @throws \ReflectionException
     */
    public function execute($request)
    {
        $fields = array_values(array_map(static function ($field) {
            return $field['name'];
        }, $request['fields']));
        $model = $request['model'];
        $fillables = $this->getFillables(array_merge((new $model)->getFillable(), $fields));

        dd($fillables);

        $file = (new ReflectionClass($model))->getFileName();
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $index = $this->getFillableIndex($lines);

        $removeLines = $this->removeLeading($lines, $index - 1);
        $removeLines = array_merge($removeLines, $this->removeTrailing($lines, $index));

        dd($request['fields'], $request['new_fields']);

//        File::put($path, str_replace(
//            ['$namespace$', '$model$', '$fillables$'],
//            [rtrim($namespace, '\\'), $model, $this->getFillables($request['fields'])],
//            File::get($stub)
//        ));
    }

    private function getFillableIndex(array $lines)
    {
        foreach ($lines as $index => $line) {
            if (strpos($line, '$fillable') !== false) {
                return $index + 1;
            }
        }

        return -1;
    }

    /**
     * @param $lines
     * @param $index
     * @return array
     */
    private function removeLeading($lines, $index): array
    {
        $line = '';
        $remove = [];

        while (!$this->startOfFillable($line)) {
            $line = $lines[$index];

            $remove[] = $index;

            $index--;
        }

        if (trim($lines[$index]) === '') {
            $remove[] = $index;
        }

        return $remove;
    }

    /**
     * @param $lines
     * @param $index
     * @return array
     */
    private function removeTrailing($lines, $index): array
    {
        $line = '';
        $remove = [];

        while (!$this->endOfFillable($line)) {
            $line = $lines[$index];

            $remove[] = $index;

            $index++;
        }

        if (trim($lines[$index]) === '') {
            $remove[] = $index;
        }

        return $remove;
    }

    /**
     * @param $line
     * @return bool
     */
    private function endOfFillable($line): bool
    {
        return str_replace(' ', '', $line) === '];';
    }

    /**
     * @param $line
     * @return bool
     */
    private function startOfFillable($line): bool
    {
        $line = str_replace(' ', '', $line);

        return $line === '/**'
            || $line === '{'
            || $line === '];'
            || $line === '}';
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
                $fillables .= "'{$field}',";
            } else {
                $fillables .= PHP_EOL . str_repeat(' ', 8) . "'{$field}',";
            }
        }

        return $fillables;
    }
}
