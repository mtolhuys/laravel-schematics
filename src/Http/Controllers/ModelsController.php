<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Mtolhuys\LaravelSchematics\Actions\CreateFormRequestAction;
use Mtolhuys\LaravelSchematics\Actions\CreateMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\CreateResourceControllerAction;
use Mtolhuys\LaravelSchematics\Actions\DeleteMigrationAction;
use Mtolhuys\LaravelSchematics\Http\Requests\CreateModelRequest;
use Mtolhuys\LaravelSchematics\Http\Requests\DeleteModelRequest;
use Mtolhuys\LaravelSchematics\Actions\CreateModelAction;
use Mtolhuys\LaravelSchematics\Actions\DeleteModelAction;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controller;

class ModelsController extends Controller
{
    /**
     * @param $request
     * @return ResponseFactory|\Illuminate\Http\Response|Response
     */
    public function create(CreateModelRequest $request)
    {
        (new CreateModelAction())->execute($request);
        (new CreateMigrationAction())->execute($request);

        $this->createOptional($request);

        Cache::forget('schematics');

        return response('Model created', 200);
    }

    /**
     * @param DeleteModelRequest $request
     * @return ResponseFactory|\Illuminate\Http\Response|Response
     * @throws \ReflectionException
     */
    public function delete(DeleteModelRequest $request)
    {
        (new DeleteModelAction())->execute($request);
        (new DeleteMigrationAction())->execute($request);

        Cache::forget('schematics');

        return response('Model deleted', 200);
    }

    /**
     * @param $request
     */
    public function createOptional($request)
    {
        foreach ($request['options'] as $option => $shouldUse) {
            if (json_decode($shouldUse, false)) {
                $this->getCreateAction($option)->execute([
                    'name' => $request['name'],
                    'model' => config('schematics.namespace') . $request['name'],
                    'fields' => self::getFields($request['fields'])
                ]);
            }
        }
    }

    /**
     * @param $option
     * @return mixed
     */
    private function getCreateAction($option)
    {
        return [
            'hasFormRequest' => new CreateFormRequestAction,
            'hasResource' => new CreateResourceControllerAction,
        ][$option];
    }
}
