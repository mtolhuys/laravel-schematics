<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Mtolhuys\LaravelSchematics\Actions\Migration\CreateRelationMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\Relation\DeleteRelationAction;
use Mtolhuys\LaravelSchematics\Actions\Relation\CreateRelationAction;
use Mtolhuys\LaravelSchematics\Actions\Migration\DeleteMigrationAction;
use Mtolhuys\LaravelSchematics\Http\Requests\CreateRelationRequest;
use Mtolhuys\LaravelSchematics\Http\Requests\DeleteRelationRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controller;
use ReflectionException;

class RelationsController extends Controller
{
    /**
     * @param CreateRelationRequest $request
     * @return array
     * @throws ReflectionException
     */
    public function create(CreateRelationRequest $request)
    {
        Cache::forget('schematics');

        $result = (new CreateRelationAction())->execute($request);
        (new CreateRelationMigrationAction())->execute($request);

        $relation = $request->all();
        $relation['method']['file'] = $result->file;
        $relation['method']['line'] = $result->line;

        return $relation;
    }

    /**
     * @param DeleteRelationRequest $request
     * @return ResponseFactory|\Illuminate\Http\Response|Response
     */
    public function delete(DeleteRelationRequest $request)
    {
        Cache::forget('schematics');

        (new DeleteRelationAction())->execute($request);
        (new DeleteMigrationAction())->execute($request);

        return response('Relation removed', 200);
    }
}
