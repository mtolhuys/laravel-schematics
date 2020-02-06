<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Mtolhuys\LaravelSchematics\Http\Requests\CreateModelRequest;
use Mtolhuys\LaravelSchematics\Http\Requests\DeleteModelRequest;
use Mtolhuys\LaravelSchematics\Actions\CreateModelAction;
use Mtolhuys\LaravelSchematics\Actions\DeleteModelAction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controller;

class ModelsController extends Controller
{
    public function create(CreateModelRequest $request)
    {
        (new CreateModelAction())->execute($request->all());

        Cache::forget('schematics');

        return response('Model created', 200);
    }

    public function delete(DeleteModelRequest $request)
    {
        (new DeleteModelAction())->execute($request->all());

        Cache::forget('schematics');

        return response('Model deleted', 200);
    }
}
