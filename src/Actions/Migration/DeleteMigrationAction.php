<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Http\Requests\DeleteRelationRequest;
use Mtolhuys\LaravelSchematics\Actions\Migration\Traits\DeletesMigrations;

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
            if (str_contains($migration, ".php") && $this->isRelatedMigration($migration, $request)) {
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

        $table = Str::plural(Str::snake(
            substr($request['name'], strrpos($request['name'], '\\') + 1)
        ));

        return strpos($content, "laravel-schematics-$table-model") !== false
            || strpos($content, "laravel-schematics-$table-relation") !== false;
    }

    /**
     * @param SplFileInfo $file
     * @return bool
     */
    private function readablePhp(SplFileInfo $file): bool
    {
        return
            $file->isReadable()
            && $file->isFile()
            && mb_strtolower($file->getExtension()) === 'php';
    }
}
