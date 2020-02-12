<?php

namespace Mtolhuys\LaravelSchematics\Actions\Model;

use Illuminate\Support\Facades\File;
use ReflectionClass;

class EditModelAction
{
    /**
     * @param $request
     * @return void
     * @throws \ReflectionException
     */
    public function execute($request)
    {
        $model = $request['model'];
        $fields = array_values(array_map(static function ($field) {
            return $field['name'];
        }, $request['fields']));
        $stub = __DIR__ . '/../../../resources/stubs/fillables.stub';
        $file = (new ReflectionClass($model))->getFileName();
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $index = $this->getFillableIndex($lines);
        $injectionLine = $index;
        $removeLines = $this->removeTrailing($lines, $index);
        $removeLines = array_merge($removeLines, $this->removeLeading($lines, $index));

        foreach ($removeLines as $removeLine) {
            $injectionLine = $removeLine + 1;
            unset($lines[$removeLine]);
        }

        $lines[$injectionLine] = PHP_EOL . $this->generateFillables($fields, $stub);

        file_put_contents($file, implode("\n", $lines));
    }

    private function generateFillables($fields, $stub)
    {
        return str_replace([
            '$fillables$'
        ], [
            $this->getFillables($fields),
        ], File::get($stub));
    }

    private function getFillableIndex(array $lines)
    {
        foreach ($lines as $index => $line) {
            if (strpos($line, '$fillable') !== false) {
                return $index;
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
        $remove = [$index];

        do {
            $index--;
            $remove[] = $index;
        } while (! $this->startOfFillable($lines[$index]));

        return $remove;
    }

    /**
     * @param $lines
     * @param $index
     * @return array
     */
    private function removeTrailing($lines, $index): array
    {
        $remove = [$index];

        do {
            $index++;
            $remove[] = $index;
        } while (! $this->endOfFillable($lines[$index]));

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
