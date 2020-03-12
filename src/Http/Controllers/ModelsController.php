<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Mtolhuys\LaravelSchematics\Http\Controllers\Traits\HasOptionalActions;
use Mtolhuys\LaravelSchematics\Http\Requests\EditModelRequest;
use Mtolhuys\LaravelSchematics\Http\Requests\CreateModelRequest;
use Mtolhuys\LaravelSchematics\Http\Requests\DeleteModelRequest;
use Mtolhuys\LaravelSchematics\Actions\Model\CreateModelAction;
use Mtolhuys\LaravelSchematics\Actions\Model\DeleteModelAction;
use Mtolhuys\LaravelSchematics\Actions\Model\EditModelAction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use ReflectionException;

class ModelsController extends Controller
{
    use HasOptionalActions;

    /**
     * @param $request
     * @return Response
     */
    public function create(CreateModelRequest $request)
    {
        (new CreateModelAction())->execute($request);

        $this->optionalActions($request);

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

        $this->optionalActions($request);

        Cache::forget('schematics');

        return response('Model deleted', 200);
    }

    /**
     * @return array
     */
    public function columns(): array
    {
        $model = request('model');
        $model = new $model;
        $table = $model->getTable();
        $safeTableName = \DB::getQueryGrammar()->wrap($table);
        
        return \Schema::hasTable($table) ? \DB::select("describe $safeTableName") : [];
    }

    /**
     * @param EditModelRequest $request
     * @return Response
     * @throws ReflectionException
     */
    public function edit(EditModelRequest $request)
    {
        (new EditModelAction())->execute($request);

        Cache::forget('schematics');

        return response('Model changed', 200);
    }
}
