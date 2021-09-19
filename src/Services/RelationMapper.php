<?php

namespace Mtolhuys\LaravelSchematics\Services;
use Mtolhuys\LaravelSchematics\Services\Pivot as PivotService;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;

use ReflectionException;
use ReflectionClass;
use ReflectionMethod;
use Config;

class RelationMapper extends ClassReader
{
	public static $relations = [];
	public static $models = [];

    /**
     * Maps relations details map through array of Eloquent models
     *
     * @param null $models
     * @return array
     * @throws ReflectionException
     */
    public static function map($models = null): array
    {
		$models = $models ?? ModelMapper::map();
		self::$models = $models;
        foreach ($models as $model) {
            self::getMethods($model)->each(static function ($method) use ($model) {
				$details = self::getDetails($method, $model);
				if ($details) {
					if (empty(self::$relations[$details->table])) {
						self::$relations[$details->table] = [];
					}
					self::$relations[$details->table][] = $details;
					
					$usePivot = Config::get('schematics.use-pivot');
					if ($usePivot && $details->type == "BelongsToMany" && isset($details->pivotsTo)){
						if (empty(self::$relations[$details->relation->table])) {
							self::$relations[$details->relation->table] = [];
						}
						self::$relations[$details->relation->table][] = $details->pivotsTo;
						unset( $details->pivotsTo);
					}
                }
            });
        }
        return self::$relations;
    }

    /**
     * @param ReflectionMethod $method
     * @param string $model
     * @return object|null
     */
    protected static function getDetails(ReflectionMethod $method, string $model)
    {
		$usePivot = Config::get('schematics.use-pivot');
        try {
            $class = app($model);
            $invocation = $method->invoke($class);

            if ($invocation instanceof Relation) {
				if ($usePivot && get_class($invocation) === "Illuminate\Database\Eloquent\Relations\BelongsToMany"){
					$pivotModel = PivotService::getPivotModel($method);
					if (!$pivotModel){
						$pivotModel = PivotService::getPivotModelFallback($class, $invocation, $method, self::$models);
					}
					if ($pivotModel){
						$dst = $invocation->getRelated();
						//TODO: neater way to create these objects
						$result = (object)[
							'model' => $model,
							'table' =>  $class->getTable(),
							'type' => 'BelongsToMany',

							'relation' => (object)[
								'model' => get_class($pivotModel),
								'table' => $pivotModel->getTable(),
								'pivotsTo' => get_class($dst),
							],
							'method' => (object)[
								'name' => $method->getName(),
								'file' => $method->getFileName(),
								'line' => $method->getStartLine(),
							],
						];
						$pivotsTo = (object)[
							'model' => $model,
							'table' =>  $class->getTable(),
							'type' => 'BelongsToMany',

							'relation' => (object)[
								'model' => get_class($dst),
								'table' => $dst->getTable(),
								'pivotsFrom' => $model
							],
							'method' => (object)[
								'model' => $model, //To make it clear in GUI that function does not come from pivot model, this should be shown in GUI
								'name' => $method->getName(),
								'file' => $method->getFileName(),
								'line' => $method->getStartLine(),
							],
						];
						$result->pivotsTo = $pivotsTo;
					} else {
						return NULL;
					}
				} else {
					$related = $invocation->getRelated();
					$result = (object)[
						'model' => $model,
						'table' => $class->getTable(),
						'type' => (new ReflectionClass($invocation))->getShortName(),
						'relation' => (object)[
							'model' => get_class($related),
							'table' => $related->getTable(),
						],
						'method' => (object)[
							'name' => $method->getName(),
							'file' => $method->getFileName(),
							'line' => $method->getStartLine(),
						],
					];
				}	
				return $result;
            }
		}
        catch (\Throwable $e) {
            return null;
        }
	}
}