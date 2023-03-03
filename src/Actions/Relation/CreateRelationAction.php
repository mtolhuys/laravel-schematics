<?php

namespace Mtolhuys\LaravelSchematics\Actions\Relation;

use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionException;

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
        $file = (new ReflectionClass(config('schematics.model.namespace') . $source))->getFileName();
        $lines = file($file, FILE_IGNORE_NEW_LINES);

        $injectionLine = $this->endOfClass($file) - 1;

        $lines[$injectionLine] = PHP_EOL . $this->generateMethod($request, $stub) . '}';

        file_put_contents($file, implode("\n", $lines));

        return (object)[
            'file' => $file,
            'line' => ($injectionLine + count(file($stub)) - 2),
        ];
    }

    /**
     * @param $file
     * @return int|string|null
     */
    private function endOfClass($file)
    {
        $lines = preg_split("/\r\n|\n|\r/", file_get_contents($file));
        $lastOccurrence = null;

        foreach ($lines as $index => $line) {
            if (strpos(trim($line), '}') !== false) {
                $lastOccurrence = $index + 1;
            }
        }

        return $lastOccurrence;
    }

    /**
     * @param $request
     * @param string $stub
     * @return string|string[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function generateMethod($request, string $stub)
    {
        $modelAsClass = json_decode($request['options']['hasModelAsClass'], false);

        $replace = [
            '$target$' => $modelAsClass ? "\\{$request['target']}::class" : "'{$request['target']}'",
            '$method$' => $request['method']['name'],
            '$type$' => lcfirst($request['type']),
            '$keys$' => $request['keys'] ?? '',
            '$class$' => $request['type'],
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            File::get($stub)
        );
    }
}
