<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Mtolhuys\LaravelSchematics\Services\ModelMapper;
use Mtolhuys\LaravelSchematics\Services\RelationMapper;
use ReflectionException;
use Illuminate\Support\Facades\Cache;

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

    /**
     * @param $model
     * @return array
     * @throws ReflectionException
     */
    public function show($model): array
    {
        $models = ModelMapper::map();

        if (isset($models[$model])) {
            return $this->modelsWithRelations([
                $models[$model]
            ]);
        }

        return [];
    }

    /**
     * @param array $models
     * @return array
     * @throws ReflectionException
     */
    private function modelsWithRelations(array $models): array
    {
        return [
            'models' => $models,
            'relations' => RelationMapper::map($models),
        ];
    }
}
