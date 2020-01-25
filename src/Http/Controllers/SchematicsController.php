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

    public function clearCache()
    {
        Cache::forget('schematics');

        return response('cache cleared', 200);
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
