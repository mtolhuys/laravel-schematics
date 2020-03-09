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

        return (object)[
            'file' => $file,
            'line' => ($injectionLine + count(file($stub)) - 2),
        ];
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

    /**
     * @param $file
     * @return int|string|null
     */
    private function endOfClass($file)
    {
        $lines = preg_split('/\r\n|\r|\n/', file_get_contents($file));
        $lastOccurrence = null;

        foreach ($lines as $index => $line) {
            if (strpos(trim($line), '}') !== false) {
                $lastOccurrence = $index + 1;
            }
        }

        return $lastOccurrence;
    }
}
