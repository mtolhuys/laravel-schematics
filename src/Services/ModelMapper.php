<?php

namespace Mtolhuys\LaravelSchematics\Services;

use SplFileInfo;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Illuminate\Database\Eloquent\Model;

class ModelMapper extends ClassReader
{
    public static $models = [];

    /**
     * Maps subclasses of Illuminate\Database\Eloquent\Model
     * found in app_path()
     *
     * @return array
     */
    public static function map(): array
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(app_path()), RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            if (self::readablePhp($file)) {
                $class = self::getClassName($file);

                if (is_subclass_of($class, Model::class)) {
                    self::$models[] = $class;
                }
            }
        }

        return self::$models;
    }

    /**
     * @param SplFileInfo $file
     * @return bool
     */
    private static function readablePhp(SplFileInfo $file): bool
    {
        return
            $file->isReadable()
            && $file->isFile()
            && mb_strtolower($file->getExtension()) === 'php';
    }
}
