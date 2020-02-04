<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Mtolhuys\LaravelSchematics\Actions\GenerateRelation;
use Mtolhuys\LaravelSchematics\Actions\RemoveRelation;
use Mtolhuys\LaravelSchematics\Http\Requests\NewRelationRequest;
use Mtolhuys\LaravelSchematics\Http\Requests\RemoveRelationRequest;
use Mtolhuys\LaravelSchematics\Services\ModelMapper;
use Mtolhuys\LaravelSchematics\Services\RelationMapper;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use ReflectionException;

class SchematicsController extends Controller
{
    /**
     * @return Factory|View
     * @throws ReflectionException
     */
    public function index()
    {
        return view('schematics::index', $this->modelsWithRelations(
            ModelMapper::map()
        ));
    }

    public function removeRelation(RemoveRelationRequest $request)
    {
        (new RemoveRelation())->execute($request->all());

        Cache::forget('schematics');

        return response('Relation removed', 200);
    }

    public function newRelation(NewRelationRequest $request)
    {
        $relation = $request->all();
        $result = (new GenerateRelation())->execute($relation);

        Cache::forget('schematics');

        $relation['method']['file'] = $result->file;
        $relation['method']['line'] = $result->line;

        return $relation;
    }

    public function clearCache()
    {
        Cache::forget('schematics');

        return response('Cache cleared', 200);
    }

    public function details($table)
    {
         $exists = Schema::hasTable($table);

         return $exists ? DB::select(DB::raw("SHOW COLUMNS FROM {$table}")) : [];
    }

    /**
     * @param array $models
     * @return array
     * @throws ReflectionException
     */
    public function modelsWithRelations(array $models = []): array
    {
        if (Cache::has('schematics')) {
            return Cache::get('schematics');
        }

        if (empty($models)) {
            $models = ModelMapper::map();
        }

        $data = [
            'models' => $models,
            'relations' => RelationMapper::map($models),
        ];

        Cache::put('schematics', $data, 1440);

        return $data;
    }
}
