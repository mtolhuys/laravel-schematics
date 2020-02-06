<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Mtolhuys\LaravelSchematics\Actions\DeleteRelationAction;
use Mtolhuys\LaravelSchematics\Actions\CreateRelationAction;
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
        $relation = $request->all();
        $result = (new CreateRelationAction())->execute($relation);

        Cache::forget('schematics');

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
        (new DeleteRelationAction())->execute($request->all());

        Cache::forget('schematics');

        return response('Relation removed', 200);
    }
}
