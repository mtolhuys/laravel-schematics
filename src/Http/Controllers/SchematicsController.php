<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
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
        return view('schematics::index', $this->modelsWithRelations(models()));
    }

    /**
     * @param $model
     * @return array
     * @throws ReflectionException
     */
    public function show($model): array
    {
        $models = models();

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
        if (Cache::has('schematics')) {
            return Cache::get('schematics');
        }

        $data = [
            'models' => $models,
            'relations' => relations($models),
        ];

        Cache::put('schematics', $data, 1440);

        return $data;
    }
}
