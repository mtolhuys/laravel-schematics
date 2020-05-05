<?php

namespace Mtolhuys\LaravelSchematics\Services;

use ReflectionClass;
use Mtolhuys\LaravelSchematics\Http\Requests\CreateRelationRequest;
use Mtolhuys\LaravelSchematics\Exceptions\PivotException;

class Pivot
{
	/**
	 * Find pivot model class for BelongsToMany
	 * 
	 * @return object|null Returns instance of pivot model when found, null on failure
	 */
	public static function getPivotModel($method)
	{
		$pattern = '/(@schematics-pivot)([\s]*)([\S]*)/';
		preg_match_all($pattern, $method->getDocComment(), $matches);
		if (isset($matches[1][0]) && $matches[1][0] == "@schematics-pivot"){
			try{
				$pivotModelName = $matches[3][0];
			} catch (\Exception $e){
				throw new PivotException("Malformed @schematics-pivot tag");
			}
			if (class_exists($pivotModelName)){
				return App($pivotModelName);
			} 
		}
		return NULL;
	}

	/**
	 * Makes an attemp at finding pivot model class for BelongsToMany without @schematics-pivot tag
	 * 
	 * @return object|null Returns instance of pivot model when found, null on failure
	 */
	public static function getPivotModelFallback($srcModel, $invocation, $method, $models)
	{

		//Assumed name is ModeloneModeltwo or modeltwoModelone in same namespace, if not, we have to do dark magic
		$reflectionOne =  new ReflectionClass($srcModel);
		$reflectionTwo = new ReflectionClass($invocation->getRelated());

		$possibleNames = [];
		$assumedNamespace = $reflectionTwo->getNamespaceName();
		$possibleNames[] = $assumedNamespace . '\\' . $reflectionOne->getShortName() . $reflectionTwo->getShortName();
		$possibleNames[] = $assumedNamespace . '\\' . $reflectionTwo->getShortName() . $reflectionOne->getShortName();

		$obj = NULL;
		$i = 0;
		for ($i = 0; $i < count($possibleNames); $i++){
			if (class_exists($possibleNames[$i])){
				$obj = app($possibleNames[$i]);
				break;
			}
		};
		if ($obj){
			return $obj;
		}

		//Did not find it the easy way, analyze code to see if we get better results in some sort of desperate attempt
		$source = file($method->getFileName());
		$startLine = $method->getStartLine();
		$endLine = $method->getEndLine();
		$nLines = $endLine - $startLine;
		$functionSrc = implode("", array_slice($source, $startLine, $nLines));

		//this only works for very simple setups, if database name is given ( $this->belongsToMany('App\A', a_b);)
		//TODO: deal more complex code ? ;_; simple regex wont help there, but is probably overkill anyway
		$pattern = '/(belongsToMany\()([\s\S]*)(,)([\s\S]*)(\))/';
		preg_match_all($pattern, $functionSrc, $matches);
		if (!isset($matches[4][0])){
			return NULL;
		}
	
		//Won't scale amazing with big databases
		foreach ($models as $model){
			$obj = app($model);
			if ("assumedSecondParam" == $obj->getTable()){
				break;
			}
		}
		return $obj;
	}

	/**
	 * @param CreateRelationRequest $request 
	 * @return string Name of pivot model
	 * @throws PivotException When pivot model does not exist.
	 */
	public static function GetPivotModelFromRequest(CreateRelationRequest $request)
	{
		if (!isset($request['pivot']) || !$request['pivot']){
			$name  = self::getDefaultPivotModelName($request['source'], $request['target']);
		} else {
			$name = $request['pivot'];
		}
		return $name;
	}

	/**
	 * Returns default modelName (including namespace) for a pivot model, based on name of target and source models.
	 * Throws exception if model with default name does not exist, so returned name is always valid class.
	 * 
	 * @param string $sourceModelName Name of source model including namespace
	 * @param string $targetModelName Name of target model including namespace
	 * @return string Name of pivot model
	 * @throws PivotException When model with default name does not exist.
	 */
	private static function getDefaultPivotModelName(string $sourceModelName,string $targetModelName)
	{
		$arr = explode('\\', $sourceModelName);
		$source = end($arr);
		$namespaceName = count($arr) > 1 ? $arr[0] : "";

		$arr = explode('\\', $targetModelName);
		$target = end($arr);

		$modelName = $namespaceName . '\\' . $target . $source;

		if (!class_exists($modelName)){
			$modelName = $namespaceName . '\\' . $source . $target;
			if (!class_exists($modelName)){
				throw new  PivotException("Pivot model with default name does not exist");
			}
		}	
		return $modelName;
	}
}
