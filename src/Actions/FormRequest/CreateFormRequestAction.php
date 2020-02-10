<?php

namespace Mtolhuys\LaravelSchematics\Actions\FormRequest;

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
