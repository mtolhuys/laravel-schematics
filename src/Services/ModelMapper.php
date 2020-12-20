<?php

namespace Mtolhuys\LaravelSchematics\Services;

use Illuminate\Database\Eloquent\Model;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

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
        $paths = config('schematics.model.paths', [app_path()]);

        foreach ($paths as $path) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($files as $file) {
                if (self::readablePhp($file)) {
                    $class = self::getClassName($file);

                    if (is_subclass_of(config('schematics.model.namespace') . $class, Model::class)) {
                        self::$models[] = $class;
                    }
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
