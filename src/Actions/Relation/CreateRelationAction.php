<?php

namespace Mtolhuys\LaravelSchematics\Actions\Relation;

use Illuminate\Support\Facades\File;
use ReflectionException;
use ReflectionClass;

class CreateRelationAction
{
    /**
     * @param $request
     * @return object
     * @throws ReflectionException
     */
    public function execute($request)
    {
        $source = $request['source'];
        $stub = __DIR__ . '/../../../resources/stubs/relation.stub';
        $file = (new ReflectionClass($source))->getFileName();
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        $injectionLine = $this->endOfClass($file) - 1;

        $lines[$injectionLine] = PHP_EOL . $this->generateMethod($request, $stub) . '}';

        file_put_contents($file, implode("\n", $lines));

        return (object) [
            'file' => $file,
            'line' => ($injectionLine + count(file($stub)) - 2),
        ];
    }

    /**
     * @param $request
     * @param string $stub
     * @return string|string[]
     */
    private function generateMethod($request, string $stub)
    {
        if (json_decode($request['options']['hasModelAsClass'], false)) {
            $relationTarget = '\\' . $request['target'] . '::class';
        } else {
            $relationTarget = '\'' . $request['target'] . '\'';
        }

        return str_replace([
            '$class$',
            '$method$',
            '$type$',
            '$target$',
            '$keys$'
        ], [
            $request['type'],
            $request['method']['name'],
            lcfirst($request['type']),
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
