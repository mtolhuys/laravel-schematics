<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Mtolhuys\LaravelSchematics\Actions\Migration\Traits\DeletesMigrations;
use Mtolhuys\LaravelSchematics\Http\Requests\DeleteRelationRequest;

class DeleteMigrationAction
{
    use DeletesMigrations;

    /**
     * @param $request
     * @return string
     */
    public function execute($request)
    {
        $migrations = scandir($this->path);

        foreach ($migrations as $migration) {
            if ($migration != '.' && $migration != '..' && $this->isRelatedMigration($migration, $request)) {
                if ($this->autoMigrate) {
                    $this->down($migration);
                }

                File::delete("$this->path/$migration");
            }
        }
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
            $this->autoMigrate = true;

            return strpos($content, "laravel-schematics-{$request['table']}-relation") !== false;
        }

        $table = Str::plural(Str::snake(substr($request['name'], strrpos($request['name'], '\\'))));

        return strpos($content, "laravel-schematics-$table-model") !== false
            || strpos($content, "laravel-schematics-$table-relation") !== false;
    }
}
