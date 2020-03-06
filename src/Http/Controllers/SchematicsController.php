<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Mtolhuys\LaravelSchematics\Models\Migration;
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
        return view('schematics::index', $this->schematics());
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
     * @return array
     */
    public function migrations(): array
    {
        $redundant = [];
        $ran = Migration::all();
        $created = glob(base_path('database/migrations') . '/*.php');

        foreach ($ran as $migration) {
            if (!in_array(base_path("database/migrations/$migration->migration.php"), $created, true)) {
                $redundant[] = $migration;
            }
        }

        return [
            'redundant' => count($redundant),
            'created' => count($created),
            'run' => count($ran),
        ];
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function refresh(): array
    {
        return $this->schematics(false);
    }

    /**
     * @param bool $cache
     * @return array
     * @throws ReflectionException
     */
    public function schematics($cache = true): array
    {
        if ($cache && Cache::has('schematics')) {
            return Cache::get('schematics');
        }

        $models = ModelMapper::map();

        $data = [
            'models' => $models,
            'relations' => RelationMapper::map($models),
            'migrations' => $this->migrations(),
        ];

        Cache::put('schematics', $data, 1440);

        return $data;
    }
}
