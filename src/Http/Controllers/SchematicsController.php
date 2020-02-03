<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Mtolhuys\LaravelSchematics\Actions\GenerateRelation;
use Mtolhuys\LaravelSchematics\Http\Requests\NewRelationRequest;
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

    public function newRelation(NewRelationRequest $request)
    {
        $success = (new GenerateRelation())->generate($request->all());

        if ($success) {
            Cache::forget('schematics');

            while (Cache::has('schematics')) {
                sleep(1);
            }

            return response("Relation {$request['method']['name']}() created", 200);
        }

        return response('Failed creating relation', 500);
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
    private function modelsWithRelations(array $models): array
    {
        if (Cache::has('schematics')) {
            return Cache::get('schematics');
        }

        $data = [
            'models' => $models,
            'relations' => RelationMapper::map($models),
        ];

        Cache::put('schematics', $data, 1440);

        return $data;
    }
}
