<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Mtolhuys\LaravelSchematics\Http\Controllers\Traits\HasOptionalActions;
use Mtolhuys\LaravelSchematics\Actions\Relation\DeleteRelationAction;
use Mtolhuys\LaravelSchematics\Actions\Relation\CreateRelationAction;
use Mtolhuys\LaravelSchematics\Http\Requests\CreateRelationRequest;
use Mtolhuys\LaravelSchematics\Http\Requests\DeleteRelationRequest;
use Mtolhuys\LaravelSchematics\Services\Pivot as PivotService;
use Mtolhuys\LaravelSchematics\Exceptions\PivotException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controller;
use ReflectionException;
use Config;

class RelationsController extends Controller
{
    use HasOptionalActions;

    /**
     * @param CreateRelationRequest $request
     * @return array
     * @throws ReflectionException
     */
    public function create(CreateRelationRequest $request)
    {
		$input = $request->input();
		if (Config::get('schematics.use-pivot')
		&& $request['type'] == 'BelongsToMany') {
			try{
				$input['pivot'] = PivotService::GetPivotModelFromRequest($request);
			} catch(PivotException $e){
				return Response($e->getMessage(), 422);
			}
		}
        $result = (new CreateRelationAction())->execute($input);

        $this->optionalActions($input);

        Cache::forget('schematics');

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
        (new DeleteRelationAction())->execute($request);

        $this->optionalActions($request);

        Cache::forget('schematics');

        return response('Relation removed', 200);
    }
}
