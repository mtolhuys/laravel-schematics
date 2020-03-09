<?php

use Mtolhuys\LaravelSchematics\Actions\Resource\CreateFormRequestAction;
use Mtolhuys\LaravelSchematics\Tests\TestCase;

class WriteFormRequestTest extends TestCase
{
    private $path;

    public function setUp(): void
    {
        parent::setUp();

        $this->path = app_path(str_replace(['App\\', '\\'], ['', '/'], $this->formRequestNamespace) . '/CreateFooBarRequest.php');

    }

    /** @test */
    public function it_successfully_creates_form_requests()
    {
        (new CreateFormRequestAction())->execute([
            'name' => 'FooBar',
            'fields' => [
                [
                    'name' => 'id',
                    'type'  => 'bigIncrements',
                ],
                [
                    'name' => 'soft_deletes',
                    'type'  => 'softDeletes',
                ],
                [
                    'name' => 'soft_deletes_tz',
                    'type'  => 'softDeletesTz',
                ],
                [
                    'name' => 'remember_token',
                    'type'  => 'rememberToken',
                ],
                [
                    'name' => 'big_id',
                    'type'  => 'bigIncrements',
                ],
                [
                    'name' => 'big_int',
                    'type'  => 'bigInteger|required',
                ],
                [
                    'name' => 'bool',
                    'type'  => 'boolean|required',
                ],
                [
                    'name' => 'date_time',
                    'type'  => 'dateTime',
                ],
                [
                    'name' => 'date_time_tz',
                    'type'  => 'dateTimeTz',
                ],
                [
                    'name' => 'date',
                    'type'  => 'date',
                ],
                [
                    'name' => 'decimal',
                    'type'  => 'decimal',
                ],
                [
                    'name' => 'enum',
                    'type'  => 'enum',
                ],
                [
                    'name' => 'geometry',
                    'type'  => 'geometry',
                ],
                [
                    'name' => 'increments',
                    'type'  => 'increments',
                ],
                [
                    'name' => 'integer',
                    'type'  => 'integer|max:50',
                ],
                [
                    'name' => 'ip',
                    'type'  => 'ipAddress',
                ],
                [
                    'name' => 'json',
                    'type'  => 'json',
                ],
                [
                    'name' => 'jsonb',
                    'type'  => 'jsonb',
                ],
                [
                    'name' => 'long_text',
                    'type'  => 'longText',
                ],
                [
                    'name' => 'mac_address',
                    'type'  => 'macAddress',
                ],
                [
                    'name' => 'medium_integer',
                    'type'  => 'mediumInteger',
                ],
                [
                    'name' => 'medium_increments',
                    'type'  => 'mediumIncrements',
                ],
                [
                    'name' => 'point',
                    'type'  => 'point',
                ],
                [
                    'name' => 'polygon',
                    'type'  => 'polygon',
                ],
                [
                    'name' => 'small_increments',
                    'type'  => 'smallIncrements',
                ],
                [
                    'name' => 'small_integer',
                    'type'  => 'smallInteger',
                ],
                [
                    'name' => 'string',
                    'type'  => 'string',
                ],
                [
                    'name' => 'text',
                    'type'  => 'text',
                ],
                [
                    'name' => 'timestamp',
                    'type'  => 'timestamp',
                ],
                [
                    'name' => 'timestamp_tz',
                    'type'  => 'timestampTz',
                ],
                [
                    'name' => 'tiny_increments',
                    'type'  => 'tinyIncrements',
                ],
                [
                    'name' => 'tiny_integer',
                    'type'  => 'tinyInteger',
                ],
                [
                    'name' => 'time',
                    'type'  => 'time',
                ],
                [
                    'name' => 'unsigned_integer',
                    'type'  => 'unsignedInteger',
                ],
                [
                    'name' => 'uuid',
                    'type'  => 'uuid',
                ],
                [
                    'name' => 'year',
                    'type'  => 'year',
                ],
            ],
        ]);

        $this->assertTrue(File::exists($this->path));

        $content = File::get($this->path);

        $this->assertStringContainsString(rtrim($this->formRequestNamespace, '\\'), $content);
        $this->assertStringContainsString('CreateFooBarRequest', $content);
        $this->assertStringContainsString('public function rules()', $content);
        $this->assertStringContainsString("'big_int' => 'numeric|required',", $content);
        $this->assertStringContainsString("'bool' => 'boolean|required',", $content);
        $this->assertStringContainsString("'date_time' => 'date',", $content);
        $this->assertStringContainsString("'date_time_tz' => 'date',", $content);
        $this->assertStringContainsString("'date' => 'date',", $content);
        $this->assertStringContainsString("'decimal' => 'numeric',", $content);
        $this->assertStringContainsString("'enum' => 'numeric',", $content);
        $this->assertStringContainsString("'geometry' => 'geometry',", $content);
        $this->assertStringContainsString("'integer' => 'numeric|max:50',", $content);
        $this->assertStringContainsString("'ip' => 'ip',", $content);
        $this->assertStringContainsString("'json' => 'json',", $content);
        $this->assertStringContainsString("'jsonb' => 'json',", $content);
        $this->assertStringContainsString("'long_text' => 'string',", $content);
        $this->assertStringContainsString("'medium_integer' => 'numeric',", $content);
        $this->assertStringContainsString("'point' => 'numeric',", $content);
        $this->assertStringContainsString("'polygon' => 'numeric',", $content);
        $this->assertStringContainsString("'small_integer' => 'numeric',", $content);
    }
}
