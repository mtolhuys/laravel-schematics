<?php

namespace Mtolhuys\LaravelSchematics\Actions;

use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Services\ColumnParser;

class GenerateMigration
{
    public $table, $rules;

    public function __construct(string $table, array $rules)
    {
        $this->table = $table;
        $this->rules = $rules;
    }

    public function execute()
    {
        $filename = 'database/migrations/' . date('Y_m_d_His') . "_create_{$this->table}_table.php";
        $classname = 'Create' . ucfirst($this->table) . 'Table';
        $columns = ColumnParser::rulesToMigrationColumns([
            'bigint(20) unsigned',
            'varchar(255)',
            'timestamp',
        ]);

        File::put(base_path($filename), str_replace(
            ['$classname$', '$table$', '$columns$'],
            [$classname, $this->table, $columns],
            File::get(__DIR__ . '/../stubs/migration.stub')
        ));
    }
}
