<?php

namespace Mtolhuys\LaravelSchematics\Actions\Relation;

use Illuminate\Support\Facades\File;
use ReflectionException;
use ReflectionClass;
use Config;
use App;

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
    protected function generateMethod($request, string $stub)
    {
        $modelAsClass = isset($request['options']['hasModelAsClass']) ? json_decode($request['options']['hasModelAsClass'], false) : false;

		$params = $modelAsClass ? "\\{$request['target']}::class" : "'{$request['target']}'";
		
		if (Config::get('schematics.use-pivot') && $request['type'] == 'BelongsToMany'){
			$tags	= '@schematics-pivot	'.$request['pivot'];
			$params.= ", '" . App($request['pivot'])->getTable() . "'";
		} 
		$params .= $request['keys'];

		$replace = [
            '$method$' => $request['method']['name'],
            '$type$' => lcfirst($request['type']),
			'$params$' => $params,
			'$class$' => $request['type'],
			'$tags$' => $tags ?? '',
		];
		
        $res = str_replace(
            array_keys($replace),
            array_values($replace),
            File::get($stub)
		);
		return $res;
    }

    /**
     * @param $file
     * @return int|string|null
     */
    protected function endOfClass($file)
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
}
