<?php

namespace Mtolhuys\LaravelSchematics\Services;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ModelMapper
{
    public static $models = [];

    /**
     * Maps subclasses of Illuminate\Database\Eloquent\Model
     * found in app_path()
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function map(): array
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(app_path()), RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            if (self::readablePhp($file)) {
                $class = 'App' . str_replace(
                        '/', "\\", mb_substr($file->getRealPath(), mb_strlen(app_path()), -4)
                    );

                if (self::isInvocableModel($class)) {
                    self::$models[] = $class;
                }
            }
        }

        return self::$models;
    }

    /**
     * @param $class
     * @return bool
     * @throws \ReflectionException
     */
    private static function isInvocableModel($class): bool
    {
        return
            class_exists($class)
            && (new \ReflectionClass($class))->isInstantiable()
            && is_subclass_of($class,'Illuminate\Database\Eloquent\Model');
    }

    /**
     * @param $file
     * @return bool
     */
    private static function readablePhp($file): bool
    {
        return
            $file->isReadable()
            && $file->isFile()
            && mb_strtolower($file->getExtension()) === 'php';
    }
}
