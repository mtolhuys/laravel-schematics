<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Mtolhuys\LaravelSchematics\Actions\Model\EditModelAction;
use Mtolhuys\LaravelSchematics\Actions\Resource\CreateResourceControllerAction;
use Mtolhuys\LaravelSchematics\Actions\Migration\CreateColumnsMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\Migration\CreateModelMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\Migration\DeleteMigrationAction;
use Mtolhuys\LaravelSchematics\Http\Requests\EditModelRequest;
use Mtolhuys\LaravelSchematics\Http\Requests\CreateModelRequest;
use Mtolhuys\LaravelSchematics\Http\Requests\DeleteModelRequest;
use Mtolhuys\LaravelSchematics\Actions\Model\CreateModelAction;
use Mtolhuys\LaravelSchematics\Actions\Model\DeleteModelAction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use ReflectionException;

class ModelsController extends Controller
{
    /**
     * @param $request
     * @return Response
     */
    public function create(CreateModelRequest $request)
    {
        (new CreateModelAction())->execute($request);
        (new CreateModelMigrationAction())->execute($request);

        $this->createOptional($request);

        Cache::forget('schematics');

        return response('Model created', 200);
    }

    /**
     * @param DeleteModelRequest $request
     * @return Response
     * @throws ReflectionException
     */
    public function delete(DeleteModelRequest $request)
    {
        (new DeleteModelAction())->execute($request);
        (new DeleteMigrationAction())->execute($request);

        Cache::forget('schematics');

        return response('Model deleted', 200);
    }

    /**
     * @param EditModelRequest $request
     * @return Response
     * @throws ReflectionException
     */
    public function edit(EditModelRequest $request)
    {
        (new EditModelAction())->execute($request);
        (new CreateColumnsMigrationAction())->execute($request);

        Cache::forget('schematics');

        return response('Model changed', 200);
    }

    /**
     * @param $request
     */
    public function createOptional($request)
    {
        foreach ($request['actions'] as $option => $shouldUse) {
            if (json_decode($shouldUse, false)) {
                $this->getCreateAction($option)->execute($request);
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
            'hasResource' => new CreateResourceControllerAction,
        ][$option];
    }
}
