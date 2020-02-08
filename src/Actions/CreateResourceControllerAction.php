<?php

namespace Mtolhuys\LaravelSchematics\Actions;

use Illuminate\Support\Facades\File;

class CreateResourceControllerAction
{
    /**
     * @param $model
     * @return void
     */
    public function execute($fields)
    {
        dd('resource', $fields);
    }
}
