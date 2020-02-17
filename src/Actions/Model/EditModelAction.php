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
        $file = (new ReflectionClass($request['model']))->getFileName();
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $index = $this->getFillableIndex($lines) - 1;

        if ($index > 0) {
            $fields = array_values(array_map(static function ($field) {
                return $field['name'];
            }, $request['fields']));
            $stub = __DIR__ . '/../../../resources/stubs/fillables.stub';
            $removeLines = array_merge(
                $this->removeTrailing($lines, $index),
                $this->removeLeading($lines, $index + 1)
            );

            sort($removeLines);
            $injectionLine = $removeLines[0];

            foreach ($removeLines as $index => $removeLine) {
                if (in_array(str_replace(' ', '', $lines[$removeLine]), ['{', '}'])) {
                    $injectionLine++;
                    continue;
                }

                unset($lines[$removeLine]);
            }

            $lines[$injectionLine] = rtrim($this->generateFillables($fields, $stub));
            ksort($lines);
            file_put_contents($file, implode("\n", $lines));

            return;
        }

        abort(422, 'Cannot replace non-existing $fillable');
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
        $remove = [];

        while (! $this->startOfFillable($lines[$index--])) {
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
        $remove = [];

        while (! $this->endOfFillable($lines[$index++])) {
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
        $stripped = str_replace(' ', '', $line);

        return $stripped === '];' || $stripped === '';
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
