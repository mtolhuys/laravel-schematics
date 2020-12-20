<?php

namespace Mtolhuys\LaravelSchematics\Services;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class ClassReader
{
    /**
     * @var bool
     */
    private static $hasNamespace;

    /**
     * @var bool
     */
    private static $hasClass;

    /**
     * @var string
     */
    private static $namespace;

    /**
     * @var string
     */
    private static $class;


    /**
     * @param string $path
     * @return string
     */
    public static function getClassName(string $path): string
    {
        self::$namespace = self::$class = '';
        self::$hasNamespace = self::$hasClass = false;

        foreach (token_get_all(file_get_contents($path)) as $token) {
            $tokenArray = is_array($token);

            self::isClass($tokenArray, $token);
            self::isNameSpace($tokenArray, $token);
            self::setNamespace($tokenArray, $token);
            self::setClassName($tokenArray, $token);

            if (self::$class) {
                break;
            }
        }

        return self::$namespace
            ? self::$namespace . '\\' . self::$class
            : self::$class;
    }

    /**
     * @param bool $tokenArray
     * @param $token
     */
    private static function isClass(bool $tokenArray, $token)
    {
        self::$hasClass = self::$hasClass || ($tokenArray && $token[0] === T_CLASS);
    }

    /**
     * @param bool $tokenArray
     * @param $token
     */
    private static function isNameSpace(bool $tokenArray, $token)
    {
        self::$hasNamespace = self::$hasNamespace || ($tokenArray && $token[0] === T_NAMESPACE);
    }

    /**
     * @param bool $tokenArray
     * @param $token
     */
    private static function setNamespace(bool $tokenArray, $token)
    {
        if (self::$hasNamespace) {
            if ($tokenArray && in_array($token[0], [T_STRING, T_NS_SEPARATOR], true)) {
                self::$namespace .= $token[1];
            } else if ($token === ';') {
                self::$hasNamespace = false;
            }
        }
    }

    /**
     * @param bool $tokenArray
     * @param $token
     */
    private static function setClassName(bool $tokenArray, $token)
    {
        if (self::$hasClass && $tokenArray && $token[0] === T_STRING) {
            self::$class = $token[1];
        }
    }

    /**
     * @param string $className
     * @return Collection
     * @throws ReflectionException
     */
    public static function getMethods(string $className): Collection
    {
        $class = new ReflectionClass(config('schematics.model.namespace') . $className);

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
