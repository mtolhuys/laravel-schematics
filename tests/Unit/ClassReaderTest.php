<?php

use Mtolhuys\LaravelSchematics\Services\ClassReader;
use Mtolhuys\LaravelSchematics\Tests\TestCase;

class ClassReaderTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_returns_classname_of_model(): void
    {
        $file = (new ReflectionClass("{$this->modelNamespace}FooBar"))->getFileName();

        $result = ClassReader::getClassName($file);

        $this->assertEquals("{$this->modelNamespace}FooBar", $result);
    }
}
