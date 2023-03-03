<?php

namespace Mtolhuys\LaravelSchematics\Actions\Model;

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
        $className = config('schematics.model.namespace') . $request['name'];
        File::delete((new ReflectionClass(app($className)))->getFileName());
    }
}
