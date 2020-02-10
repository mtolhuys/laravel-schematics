<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Mtolhuys\LaravelSchematics\Actions\FormRequest\CreateFormRequestAction;
use Mtolhuys\LaravelSchematics\Actions\Migration\CreateMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\Resource\CreateResourceControllerAction;
use Mtolhuys\LaravelSchematics\Actions\Migration\DeleteMigrationAction;
use Mtolhuys\LaravelSchematics\Http\Requests\CreateModelRequest;
use Mtolhuys\LaravelSchematics\Http\Requests\DeleteModelRequest;
use Mtolhuys\LaravelSchematics\Actions\Model\CreateModelAction;
use Mtolhuys\LaravelSchematics\Actions\Model\DeleteModelAction;
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
        (new CreateMigrationAction())->execute($request);

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
        (new DeleteMigrationAction())->execute($request);

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
     * @param $option
     * @return mixed
     */
    private function getCreateAction($option)
    {
        return [
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
