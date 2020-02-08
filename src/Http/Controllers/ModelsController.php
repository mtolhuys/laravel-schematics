<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Mtolhuys\LaravelSchematics\Actions\CreateFormRequestAction;
use Mtolhuys\LaravelSchematics\Actions\CreateMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\CreateResourceControllerAction;
use Mtolhuys\LaravelSchematics\Actions\DeleteMigrationAction;
use Mtolhuys\LaravelSchematics\Http\Requests\CreateModelRequest;
use Mtolhuys\LaravelSchematics\Http\Requests\DeleteModelRequest;
use Mtolhuys\LaravelSchematics\Actions\CreateModelAction;
use Mtolhuys\LaravelSchematics\Actions\DeleteModelAction;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controller;

class ModelsController extends Controller
{
    /**
     * @param $request
     * @return ResponseFactory|\Illuminate\Http\Response|Response
     */
    public function create(CreateModelRequest $request)
    {
        (new CreateModelAction())->execute($request);

        $this->createOptional($request);

        Cache::forget('schematics');

        return response('Model created', 200);
    }

    /**
     * @param DeleteModelRequest $request
     * @return ResponseFactory|\Illuminate\Http\Response|Response
     * @throws \ReflectionException
     */
    public function delete(DeleteModelRequest $request)
    {
        (new DeleteModelAction())->execute($request);

        $this->deleteOptional($request);

        Cache::forget('schematics');

        return response('Model deleted', 200);
    }

    /**
     * @param $request
     */
    public function createOptional($request)
    {
        foreach ($request['options'] as $option => $shouldUse) {
            if (json_decode($shouldUse, false)) {
                $this->getCreateAction($option)->execute([
                    'name' => $request['name'],
                    'model' => config('schematics.namespace') . $request['name'],
                    'fields' => self::getFields($request['fields'])
                ]);
            }
        }
    }

    /**
     * @param $request
     */
    public function deleteOptional($request)
    {
        foreach ($request['options'] as $option => $shouldUse) {
            if (json_decode($shouldUse, false)) {
                $this->getDeleteAction($option)->execute([
                    'name' => $request['name'],
                ]);
            }
        }
    }

    /**
     * @param $option
     * @return mixed
     */
    private function getCreateAction($option)
    {
        return [
            'hasMigration' => new CreateMigrationAction,
            'hasFormRequest' => new CreateFormRequestAction,
            'hasResource' => new CreateResourceControllerAction,
        ][$option];
    }

    /**
     * @param $option
     * @return mixed
     */
    private function getDeleteAction($option)
    {
        return [
            'hasMigration' => new DeleteMigrationAction,
        ][$option];
    }

    /**
     * @param $fields
     * @return array
     */
    private static function getFields($fields): array
    {
        return array_merge(
            ...array_values(array_map(static function ($field) {
                return [$field['name'] => self::getFieldType($field['type'] ?? '')];
            }, $fields))
        );
    }

    /**
     * @param $type
     * @return string
     */
    private static function getFieldType(string $type): string
    {
        return $type === '' ? 'string|max:255' : $type;
    }
}
