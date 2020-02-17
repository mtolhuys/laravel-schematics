<?php

use Mtolhuys\LaravelSchematics\Actions\Migration\CreateRelationMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\Migration\CreateColumnsMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\Migration\CreateModelMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\Migration\DeleteMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\Model\DeleteModelAction;
use Mtolhuys\LaravelSchematics\Actions\Model\CreateModelAction;
use Mtolhuys\LaravelSchematics\Tests\TestCase;
use Illuminate\Support\Facades\File;

class WriteMigrationsTest extends TestCase
{
    private $fields = [
        [
            'id' => '587a0014-6e83-482d-a1ac-7b25b0e4dc10',
            'exists' => 'false',
            'error' => 'false',
            'changed' => 'false',
            'name' => 'id',
            'type' => 'increments',
            'columnType' => 'int(10) unsigned'
        ],
        [
            'id' => '587a0922-6e83-482d-a1ac-7b25b0e4dc10',
            'exists' => 'false',
            'error' => 'false',
            'changed' => 'false',
            'name' => 'name',
            'type' => null,
            'columnType' => null
        ]
    ],
        $namespace,
        $relationMigration,
        $columnMigration,
        $modelMigration;

    public function setUp(): void
    {
        parent::setUp();

        $this->namespace = config('schematics.model-namespace');

        $this->createModels();
        $this->createsMigrations();
    }

    /**
     * @throws ReflectionException
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->deleteModels();
    }

    /** @test */
    public function it_successfully_created_model_migration()
    {
        $this->assertTrue(File::exists($this->modelMigration));

        $content = File::get($this->modelMigration);

        $this->assertStringContainsString('laravel-schematics-foo_bars-model', $content);
        $this->assertStringContainsString('CreateFooBarsTable', $content);
        $this->assertStringContainsString('Schema::create(\'foo_bars\',', $content);
        $this->assertStringContainsString('$table->increments(\'id\');', $content);
        $this->assertStringContainsString('$table->string(\'name\', 255)->nullable();', $content);
        $this->assertStringContainsString('$table->timestamps();', $content);
    }

    /** @test */
    public function it_successfully_created_column_migration()
    {
        $this->assertTrue(File::exists($this->columnMigration));

        $content = File::get($this->columnMigration);

        $this->assertStringContainsString('laravel-schematics-foo_bars-model', $content);
        $this->assertStringContainsString('CreatedEmailChangedNameDeletedIdColumnInFooBarsTable', $content);
        $this->assertStringContainsString('Schema::table(\'foo_bars\',', $content);
        $this->assertStringContainsString('$table->text(\'name\')->change();', $content);
        $this->assertStringContainsString('$table->string(\'email\', 255)->nullable();', $content);
        $this->assertStringContainsString('$table->dropColumn(\'id\');', $content);
        $this->assertStringContainsString('$table->string(\'name\')->nullable()->change();', $content);
        $this->assertStringContainsString('$table->integer(\'id\', 10)->nullable();', $content);
        $this->assertStringContainsString('$table->dropColumn(\'email\');', $content);
    }

    /** @test */
    public function it_successfully_created_relation_migration()
    {
        $this->assertTrue(File::exists($this->relationMigration));

        $content = File::get($this->relationMigration);

        $this->assertStringContainsString('laravel-schematics-bar_foos-relation', $content);
        $this->assertStringContainsString('laravel-schematics-foo_bars-relation', $content);
        $this->assertStringContainsString('CreateBarFoosFooBarsRelation', $content);
        $this->assertStringContainsString('Schema::table(\'bar_foos\',', $content);
        $this->assertStringContainsString('$table->foreign(\'bar\')->references(\'foo\')->on(\'foo_bars\');', $content);
        $this->assertStringContainsString('$table->dropForeign([\'bar\']);', $content);
        $this->assertStringContainsString('$table->dropColumn(\'bar\');', $content);
    }

    /** @test */
    public function it_successfully_deletes_model_migration()
    {
        $action = new DeleteMigrationAction;

        $action->execute([
            'name' => "{$this->namespace}FooBar",
        ]);

        $this->assertFalse(File::exists($this->relationMigration));
        $this->assertFalse(File::exists($this->columnMigration));
        $this->assertFalse(File::exists($this->modelMigration));
    }

    private function createModels(): void
    {
        (new CreateModelAction())->execute([
            'name' => 'FooBar',
            'fields' => $this->fields,
        ]);

        (new CreateModelAction())->execute([
            'name' => 'BarFoo',
            'fields' => $this->fields,
        ]);
    }

    private function createsMigrations(): void
    {
        $model = new CreateModelMigrationAction;
        $relation = new CreateRelationMigrationAction;
        $columns = new CreateColumnsMigrationAction;

        $model->execute([
            'name' => 'FooBar',
            'fields' => $this->fields,
            'options' => [
                'hasTimestamps' => 'true'
            ],
        ]);

        $this->fields[1]['changed'] = 'true';
        $this->fields[1]['exists'] = 'true';
        $this->fields[1]['type'] = 'text|required';
        $create = [
            'id' => '902a0922-6e83-482d-k0px-7b25b0e4dc10',
            'exists' => 'false',
            'error' => 'false',
            'changed' => 'false',
            'name' => 'email',
            'type' => null,
            'columnType' => null
        ];

        $columns->execute([
            'model' => "{$this->namespace}FooBar",
            'fields' => array_merge($this->fields, [$create]),
            'created' => [$create],
            'changed' => [$this->fields[1]],
            'deleted' => [$this->fields[0]]
        ]);

        $relation->execute([
            'type' => 'HasMany',
            'source' => "{$this->namespace}BarFoo",
            'target' => "{$this->namespace}FooBar",
            'method' => [
                'localKey' => 'foo',
                'foreignKey' => 'bar',
            ],
        ]);

        $this->modelMigration = base_path($model->filename);
        $this->columnMigration = base_path($columns->filename);
        $this->relationMigration = base_path($relation->filename);
    }

    /**
     * @throws ReflectionException
     */
    private function deleteModels(): void
    {
        (new DeleteModelAction())->execute([
            'name' => "{$this->namespace}FooBar",
        ]);

        (new DeleteModelAction())->execute([
            'name' => "{$this->namespace}BarFoo",
        ]);
    }
}
