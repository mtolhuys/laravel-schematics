<?php

namespace Mtolhuys\LaravelSchematics\Services;

use Illuminate\Database\Eloquent\Relations\Relation;

use ReflectionException;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionMethod;

class RelationMapper
{
    public static $relations = [];

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

        foreach ($models as $model) {
            foreach (self::getMethods($model) as $method) {
                $details = self::getDetails($method, $model);

                if ($details) {
                    if (empty(self::$relations[$details->table])) {
                        self::$relations[$details->table] = [];
                    }

                    self::$relations[$details->table][] = $details;
                }
            }
        }

        return self::$relations;
    }

    /**
     * @param string $model
     * @return Collection
     * @throws ReflectionException
     */
    public static function getMethods(string $model): Collection
    {
        $class = new ReflectionClass($model);

        return Collection::make($class->getMethods(ReflectionMethod::IS_PUBLIC))
            ->merge(Collection::make($class->getTraits())
                ->map(static function (ReflectionClass $trait) {
                    return Collection::make(
                        $trait->getMethods(ReflectionMethod::IS_PUBLIC)
                    );
                })->flatten()
            )
            ->reject(static function (ReflectionMethod $method) use ($model) {
                return $method->class !== $model || $method->getNumberOfParameters() > 0;
            });
    }

    /**
     * @param ReflectionMethod $method
     * @param string $model
     * @return object|null
     */
    protected static function getDetails(ReflectionMethod $method, string $model)
    {
        try {
            $class = app($model);
            $invocation = $method->invoke($class);

            if ($invocation instanceof Relation) {
                $related = $invocation->getRelated();

                return (object)[
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
        } catch (\Throwable $e) {}

        return null;
    }

}
