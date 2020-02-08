<?php

namespace Mtolhuys\LaravelSchematics\Actions;

use Illuminate\Support\Facades\File;

class CreateFormRequestAction
{
    /**
     * @param $model
     * @return void
     */
    public function execute($fields)
    {
        dd('form request', $fields);
    }
}
