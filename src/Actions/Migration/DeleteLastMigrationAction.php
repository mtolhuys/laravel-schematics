<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration;

use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Actions\Migration\Traits\DeletesMigrations;

class DeleteLastMigrationAction
{
    use DeletesMigrations;

    /**
     * @return string
     */
    public function execute()
    {
        $migrations = scandir($this->path);

        foreach ($migrations as $index => $migration) {
            if ($index === count($migrations) - 1) {
                File::delete("$this->path/$migration");
            }
        }
    }
}
