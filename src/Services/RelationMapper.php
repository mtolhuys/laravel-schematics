<?php

namespace Mtolhuys\LaravelSchematics\Services;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class RelationMapper
{
    public static $relations = [];

    public function __construct()
    {
        set_error_handler(static function ($severity, $message, $file, $line) {
            throw new \ErrorException($message, $severity, $severity, $file, $line);
        });
    }

    public function __destruct()
    {
        restore_error_handler();
    }

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
            $instance = new $model;
            $reflection = (new ReflectionClass($instance));

            if ($reflection->isAbstract()) {
                continue;
            }

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (self::notInvocable($method, $instance)) {
                    continue;
                }

                $invocation = self::getInvocation($method, $instance);

                if ($invocation instanceof Relation) {
                    $table = $instance->getTable();

                    if (empty(self::$relations[$table])) {
                        self::$relations[$table] = [];
                    }

                    self::$relations[$table][] = self::getDetails($model, $method, $invocation);
                }
            }
        }

        return self::$relations;
    }

    /**
     * Check if given method is impossible to invoke as relation
     *
     * @param $method
     * @param $instance
     * @return bool
     */
    private static function notInvocable($method, $instance): bool
    {
        return
            $method->class !== get_class($instance)
            || !empty($method->getParameters())
            || $method->getName() === __FUNCTION__;
    }

    private static function getInvocation($method, $instance)
    {
        try {
            return $method->invoke($instance);
        }
        catch (\Error $e) {}
        catch (\ErrorException $e) {}
        catch (QueryException $e) {}
        catch (\BadMethodCallException $e) {}

        return null;
    }

    /**
     * Get all required details about discovered relation
     *
     * @param string $model
     * @param ReflectionMethod $method
     * @param $invocation
     * @return object
     * @throws ReflectionException
     */
    private static function getDetails(string $model, ReflectionMethod $method, $invocation)
    {
        $related = $invocation->getRelated();

        return (object)[
            'model' => $model,
            'table' => $related->getTable(),
            'relation' => get_class($related),
            'type' => (new ReflectionClass($invocation))->getShortName(),
            'method' => (object)[
                'name' => $method->getName(),
                'file' => $method->getFileName(),
                'line' => $method->getStartLine(),
            ],
        ];
    }
}
