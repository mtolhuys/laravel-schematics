<?php

namespace Mtolhuys\LaravelSchematics\Services;

use Illuminate\Support\Collection;
use ReflectionException;
use ReflectionMethod;
use ReflectionClass;

class ClassReader
{
    /**
     * @param string $path
     * @return string
     */
    public static function getClassName(string $path): string
    {
        $namespace = $class = '';
        $contents = file_get_contents($path);
        $hasNamespace = $hasClass = false;

        foreach (token_get_all($contents) as $token) {
            $tokenArray = is_array($token);
            $hasNamespace = $hasNamespace || ($tokenArray && $token[0] === T_NAMESPACE);
            $hasClass = $hasClass || ($tokenArray && $token[0] === T_CLASS);

            if ($hasNamespace) {
                if ($tokenArray && in_array($token[0], [T_STRING, T_NS_SEPARATOR], true)) {
                    $namespace .= $token[1];
                } else if ($token === ';') {
                    $hasNamespace = false;
                }
            }

            if ($hasClass && $tokenArray && $token[0] === T_STRING) {
                $class = $token[1];
                break;
            }
        }

        return $namespace ? $namespace . '\\' . $class : $class;
    }

    /**
     * @param string $className
     * @return Collection
     * @throws ReflectionException
     */
    public static function getMethods(string $className): Collection
    {
        $class = new ReflectionClass($className);

        return Collection::make($class->getMethods(ReflectionMethod::IS_PUBLIC))
            ->merge(Collection::make($class->getTraits())
                ->map(static function (ReflectionClass $trait) {
                    return Collection::make(
                        $trait->getMethods(ReflectionMethod::IS_PUBLIC)
                    );
                })->flatten()
            )
            ->reject(static function (ReflectionMethod $method) use ($className) {
                return $method->class !== $className || $method->getNumberOfParameters() > 0;
            });
    }
}
