<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Actions\Migration\Traits\CreatesMigrations;
use Mtolhuys\LaravelSchematics\Services\StubWriter;
use Illuminate\Http\Response;
use Config;

class CreateRelationMigrationAction
{
    use CreatesMigrations;

    /**
     * @param $request
     * @return void
     */
    public function execute($request)
    {
		$target = app($request['target'])->getTable();
		$foreignKey = $this->getForeignKey($request);
		$localKey = $this->getLocalKey($request);
		$source = app($request['source'])->getTable();
		if (Config::get('schematics.use-pivot') && $request['type'] == 'BelongsToMany'){
			$secondForeignKey = $this->getSecondForeignKey($request);
			$secondLocalKey = $this->getSecondLocalKey($request);
			
			$column = "\$table->foreign('$foreignKey')->references('$localKey')->on('$target');" . PHP_EOL .
			"            \$table->foreign('$secondForeignKey')->references('$secondLocalKey')->on('$source');";
			$table = app($request['pivot'])->getTable();
		} else{
			$column = "\$table->foreign('$foreignKey')->references('{$this->getLocalKey($request)}')->on('$target');";
			$table = $source;
		}
		$this->filename = 'database/migrations/'
		. date('Y_m_d_His')
		. "_create_{$source}_{$target}_relation.php";
		$stub = __DIR__ . '/../../../resources/stubs/migration/relation.stub';
			
		(new StubWriter(base_path($this->filename), $stub))->write([
			'$classname$' => 'Create' . ucfirst(Str::camel($source)) . ucfirst(Str::camel($target)) . 'Relation',
			'$foreignKey$' => $foreignKey,
			'$table$' => $table,
			'$target$' => $target,
			'$column$' => $column,
		]);
		
    }
}
