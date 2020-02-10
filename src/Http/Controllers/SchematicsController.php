<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Mtolhuys\LaravelSchematics\Services\RelationMapper;
use Mtolhuys\LaravelSchematics\Services\ModelMapper;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;
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
        return view('schematics::index', $this
            ->modelsWithRelations(ModelMapper::map()));
    }

    /**
     * @return ResponseFactory|\Illuminate\Http\Response|Response
     */
    public function clearCache()
    {
        Cache::forget('schematics');
        Cache::forget('schematics-exception');

        return response('Cache cleared', 200);
    }

    /**
     * @param $table
     * @return array
     */
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
//        if (! empty($models) && Cache::has('schematics')) {
//            return Cache::get('schematics');
//        }

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
