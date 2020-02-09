<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Http\Requests\DeleteRelationRequest;

class DeleteMigrationAction
{
    public $path;

    public function __construct()
    {
        $this->path = base_path('database/migrations/');
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
                File::delete("$this->path/$migration");
            }
        }
    }

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
