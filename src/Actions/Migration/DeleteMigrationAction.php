<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Http\Requests\DeleteRelationRequest;
use Mtolhuys\LaravelSchematics\Models\Migration;
use Mtolhuys\LaravelSchematics\Services\ClassReader;

class DeleteMigrationAction
{
    public
        $autoMigrate,
        $path;

    public function __construct()
    {
        $this->autoMigrate = config('schematics.auto-migrate');
        $this->path = base_path('database/migrations');
    }

    /**
     * @param $request
     * @return string
     */
    public function execute($request)
    {
        $migrations = scandir($this->path);

        foreach ($migrations as $migration) {
            if ($this->isRelatedMigration($migration, $request)) {
                if ($this->autoMigrate) {
                    $this->down($migration);
                }

                File::delete("$this->path/$migration");
            }
        }
    }

    /**
     * Running down in case auto-migrate is turned on
     *
     * @param $migration
     */
    public function down($migration): void
    {
        $file = "$this->path/$migration";

        require_once $file;

        Migration::where('migration', pathinfo($migration, PATHINFO_FILENAME))->delete();

        $migration = ClassReader::getClassName($file);

        try {
            (new $migration)->down();
        } catch (\Throwable $e) {}
    }

    /**
     * @param $migration
     * @param $request
     * @return bool
     */
    private function isRelatedMigration($migration, $request): bool
    {
        $content = file_get_contents("$this->path/$migration");

        if ($request instanceof DeleteRelationRequest) {
            return strpos($content, "laravel-schematics-{$request['table']}-relation") !== false;
        }

        $table = Str::plural(Str::snake(
            substr($request['name'], strrpos($request['name'], '\\') + 1)
        ));

        return strpos($content, "laravel-schematics-$table-model") !== false
            || strpos($content, "laravel-schematics-$table-relation") !== false;
    }
}
