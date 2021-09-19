<?php

namespace Mtolhuys\LaravelSchematics\Actions\Relation;

use ReflectionClass;
use Config;

use Mtolhuys\LaravelSchematics\Actions\Relation\CreateRelationAction;

class CreatePivotRelationsAction extends CreateRelationAction
{
    public function execute($request)
    {		
        $stub = __DIR__ . '/../../../resources/stubs/relation.stub';
		
		$this->createRelation(
			$stub, 
			$request['pivot']['source'], 
			$request['pivot']['target'],
			" , '" . $request['pivot']['foreignKeys']['second'] . "' , '" . $request['pivot']['foreignKeys']['first'] . "'",	
			$request['pivot']['methods']['first'],
			'BelongsToMany',
			Config::get('schematics.model.namespace') . $request['name']
		);

		$this->createRelation(
			$stub, 
			$request['pivot']['target'], 
			$request['pivot']['source'],
			" , '" . $request['pivot']['foreignKeys']['first'] . "' , '" . $request['pivot']['foreignKeys']['second'] . "'",	
			$request['pivot']['methods']['second'],
			'BelongsToMany',
			Config::get('schematics.model.namespace') . $request['name']
		);
	}
	
	//TODO: should be place in parent so it can also be used there
	private function createRelation($stub, $source, $target, $keys, $methodName, $type, $pivotModelName = null)
	{
		$file = (new ReflectionClass($source))->getFileName();
        $lines = file($file, FILE_IGNORE_NEW_LINES);
		$injectionLine = $this->endOfClass($file) - 1;
		$pivotTable = App($pivotModelName)->getTable();
		$generateMethodParams = [
			'type' => $type,
			'source' => $source,
			'target' => $target,
			'keys'=> $keys,
			'method' => ['name' => $methodName],
			'pivot' => $pivotModelName,
		];
        $lines[$injectionLine] = PHP_EOL . $this->generateMethod($generateMethodParams, $stub) . '}';
		file_put_contents($file, implode("\n", $lines));
	}
}
