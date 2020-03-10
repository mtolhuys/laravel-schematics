<?php

use Mtolhuys\LaravelSchematics\Services\ModelMapper;
use Mtolhuys\LaravelSchematics\Services\RelationMapper;
use Mtolhuys\LaravelSchematics\Tests\TestCase;

/*
 * Results are based on the pre-copy-pasted models
 * found in resources/app using composer script
 */
class MappersTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_maps_models_from_multiple_paths()
    {
        $models = ModelMapper::map();

        $this->assertContains('App\FooBar', $models);
        $this->assertContains('App\BarFoo', $models);
        $this->assertContains('Src\Wombat', $models);
        $this->assertContains('Src\Acme', $models);
    }

    /**
     * @test
     */
    public function it_successfully_maps_relations_from_multiple_paths()
    {
        $relations = RelationMapper::map();

        $this->assertArrayHasKey('bar_foos', $relations);
        $this->assertIsArray($relations['bar_foos']);
        $this->assertArrayHasKey('wombats', $relations);
        $this->assertIsArray($relations['wombats']);

        $relation = $relations['bar_foos'][0];

        $this->assertIsObject($relation);

        $this->assertEquals('App\BarFoo', $relation->model);
        $this->assertEquals('bar_foos', $relation->table);
        $this->assertEquals('HasMany', $relation->type);

        $this->assertIsObject($relation->relation);
        $this->assertEquals('App\FooBar', $relation->relation->model);
        $this->assertEquals('foo_bars', $relation->relation->table);

        $this->assertIsObject($relation->method);
        $this->assertEquals('fooBars', $relation->method->name);
        $this->assertStringContainsString('app/BarFoo.php', $relation->method->file);
        $this->assertEquals(22, $relation->method->line);

        $relation = $relations['wombats'][0];

        $this->assertIsObject($relation);

        $this->assertEquals('Src\Wombat', $relation->model);
        $this->assertEquals('wombats', $relation->table);
        $this->assertEquals('BelongsTo', $relation->type);

        $this->assertIsObject($relation->relation);
        $this->assertEquals('Src\Acme', $relation->relation->model);
        $this->assertEquals('acmes', $relation->relation->table);

        $this->assertIsObject($relation->method);
        $this->assertEquals('acmes', $relation->method->name);
        $this->assertStringContainsString('src/Wombat.php', $relation->method->file);
        $this->assertEquals(22, $relation->method->line);
    }
}
