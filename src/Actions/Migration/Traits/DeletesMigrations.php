<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration\Traits;

use Mtolhuys\LaravelSchematics\Models\Migration;
use Mtolhuys\LaravelSchematics\Services\ClassReader;

trait DeletesMigrations
{
    public
        $autoMigrate,
        $filename,
        $path;

    public function __construct()
    {
        $this->autoMigrate = config('schematics.auto-migrate');
        $this->path = database_path('migrations');
    }

    /**
     * Running down in case auto-migrate is turned on
     *
     * @param $migration
     */
    public function down($migration)
    {
        $file = "$this->path/$migration";

        require_once $file;

        Migration::where('migration', pathinfo($migration, PATHINFO_FILENAME))->delete();

        $migration = ClassReader::getClassName($file);

        try {
            (new $migration)->down();
        }
        /**
         * Dont stop if the down method throws an exception
         */
        catch (\Throwable $e) {}
    }
}
