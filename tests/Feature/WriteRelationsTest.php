<?php

use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Actions\Model\CreateModelAction;
use Mtolhuys\LaravelSchematics\Actions\Relation\CreateRelationAction;
use Mtolhuys\LaravelSchematics\Actions\Relation\DeleteRelationAction;
use Mtolhuys\LaravelSchematics\Tests\TestCase;

class WriteRelationsTest extends TestCase
{
    private $path;

    public function setUp(): void
    {
        parent::setUp();

        $this->path = app_path(str_replace(['App\\', '\\'], ['', '/'], $this->modelNamespace) . 'Acme.php');

        (new CreateModelAction())->execute([
            'name' => 'Acme',
            'fields' => [],
        ]);

        (new CreateModelAction())->execute([
            'name' => 'Mace',
            'fields' => [],
        ]);
    }

    /** @test */
    public function it_successfully_creates_relations()
    {
        (new CreateRelationAction())->execute([
            'type' => 'belongsTo',
            'source' => "{$this->modelNamespace}Acme",
            'target' => "{$this->modelNamespace}Mace",
            'method' => [
                'name' => 'maces'
            ],
            'options' => [
                'hasModelAsClass' => false
            ],
        ]);

        $this->assertTrue(File::exists($this->path));

        $content = File::get($this->path);

        $this->assertStringContainsString('public function maces()', $content);
        $this->assertStringContainsString('return $this->belongsTo(\'App\Mace\');', $content);
    }

    /**
     * @depends it_successfully_creates_relations
     * @test
     */
    public function it_successfully_deletes_relations()
    {
        (new CreateRelationAction())->execute([
            'type' => 'belongsTo',
            'source' => "{$this->modelNamespace}Acme",
            'target' => "{$this->modelNamespace}Mace",
            'method' => [
                'name' => 'maces'
            ],
            'options' => [
                'hasModelAsClass' => false
            ],
        ]);

        (new DeleteRelationAction())->execute([
            'method' => [
                'name' => 'maces',
                'file' => $this->path,
                'line' => 22,
            ]
        ]);

        $this->assertTrue(File::exists($this->path));

        $content = File::get($this->path);

        $this->assertStringNotContainsString('public function maces()', $content);
        $this->assertStringNotContainsString('return $this->belongsTo(\'App\Mace\');', $content);
    }
}
