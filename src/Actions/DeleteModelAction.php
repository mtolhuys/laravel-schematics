<?php

namespace Mtolhuys\LaravelSchematics\Actions;

use Illuminate\Support\Facades\File;
use ReflectionClass;

class DeleteModelAction
{
    /**
     * @param $request
     * @return void
     * @throws \ReflectionException
     */
    public function execute($request)
    {
        File::delete(
            (new ReflectionClass($request['name']))->getFileName()
        );
    }
}
