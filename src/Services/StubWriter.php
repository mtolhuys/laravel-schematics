<?php

namespace Mtolhuys\LaravelSchematics\Services;

use Illuminate\Support\Facades\File;

class StubWriter
{
    protected $stub;

    protected $file;

    public function __construct(string $file, string $stub)
    {
        $this->stub = $stub;
        $this->file = $file;
    }

    public function write(array $variables)
    {
        if (! File::isDirectory(dirname($this->file))) {
            File::makeDirectory(dirname($this->file), 0777, true, true);
        }

        File::put($this->file, str_replace(
            array_keys($variables),
            array_values($variables),
            File::get($this->stub)
        ));
    }
}
