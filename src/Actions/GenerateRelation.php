<?php

namespace Mtolhuys\LaravelSchematics\Actions;

use Illuminate\Support\Facades\File;
use ReflectionException;
use ReflectionClass;

class GenerateRelation
{
    /**
     * @param array $relation
     * @return object
     * @throws ReflectionException
     */
    public function execute($relation)
    {
        $source = $relation['source'];
        $stub = __DIR__ . '/../../resources/stubs/relation.stub';
        $file = (new ReflectionClass($source))->getFileName();
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $injectionLine = $this->endOfClass($file) - 1;

        $lines[$injectionLine] = PHP_EOL . $this->generateMethod($relation, $stub) . '}';

        file_put_contents($file, implode("\n", $lines));

        return (object) [
            'file' => $file,
            'line' => ($injectionLine + count(file($stub)) - 2),
        ];
    }

    /**
     * @param array $relation
     * @param string $stub
     * @return string|string[]
     */
    private function generateMethod(array $relation, string $stub)
    {
        $relationGeneratorMethod = config('schematics.relation-generator-method', 'string');
        if ($relationGeneratorMethod === 'class') {
            $relationTarget = '\\' . $relation['target'] . '::class';
        } else {
            $relationTarget = '\'' . $relation['target'] . '\'';
        }

        return str_replace([
            '$class$',
            '$method$',
            '$type$',
            '$target$',
            '$keys$'
        ], [
            $relation['type'],
            $relation['method']['name'],
            lcfirst($relation['type']),
            $relationTarget,
            $relation['keys'] ?? '',
        ], File::get($stub));
    }

    /**
     * @param $file
     * @return int|string|null
     */
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
