<?php

namespace Mtolhuys\LaravelSchematics\Actions;

use Illuminate\Support\Facades\File;
use ReflectionClass;

class DeleteModelAction
{
    /**
     * @param $model
     * @return void
     * @throws \ReflectionException
     */
    public function execute($model)
    {
        $path = (new ReflectionClass($model['name']))->getFileName();

        File::delete($path);
    }
}
