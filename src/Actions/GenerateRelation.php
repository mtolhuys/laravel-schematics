<?php

namespace Mtolhuys\LaravelSchematics\Actions;

use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Services\ColumnParser;
use ReflectionClass;

class GenerateRelation
{
    /**
     * @param array $relation
     * @return bool
     * @throws \ReflectionException
     */
    public function generate($relation): bool
    {
        $source = $relation['source'];
        $file = (new ReflectionClass($source))->getFileName();
        $lines = file($file , FILE_IGNORE_NEW_LINES);
        $content = str_replace(
            ['$class$', '$method$', '$type$', '$target$', '$keys$'],
            [
                $relation['type'],
                $relation['method']['name'],
                lcfirst($relation['type']),
                $relation['target'],
                $relation['keys'] ?? '',
            ],
            File::get(__DIR__.'/../../resources/stubs/relation.stub')
        );

        $lines[$this->endOfClass($file) - 1] = PHP_EOL . $content. '}';

        return file_put_contents($file , implode( "\n", $lines));
    }

    private function endOfClass($file)
    {
        $lines = explode(PHP_EOL, file_get_contents($file));
        $lastOccurrence = null;

        foreach ($lines as $index => $line) {
            if (strpos(trim($line), '}') !== false) {
                $lastOccurrence = $index + 1;
            }
        }

        return $lastOccurrence;
    }
}
