<?php

namespace Mtolhuys\LaravelSchematics\Actions\Resource;

use Illuminate\Support\Facades\Artisan;

class CreateResourceControllerAction
{
    /**
     * @param $request
     * @return string
     */
    public function execute($request)
    {
        $model = $request['name'];
        $name = config('schematics.controller-namespace')."{$model}Controller";

        Artisan::call('make:controller', [
            'name' => $name,
            '--resource' => true,
            '--model' => config('schematics.model-namespace').$model,
        ]);

        return Artisan::output();
    }
}
