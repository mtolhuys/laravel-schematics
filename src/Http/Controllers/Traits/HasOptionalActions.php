<?php

namespace Mtolhuys\LaravelSchematics\Http\Controllers\Traits;

use Mtolhuys\LaravelSchematics\Actions\Migration\CreateColumnsMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\Migration\CreateModelMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\Migration\CreateRelationMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\Migration\DeleteMigrationAction;
use Mtolhuys\LaravelSchematics\Actions\Resource\CreateFormRequestAction;
use Mtolhuys\LaravelSchematics\Actions\Resource\CreateResourceControllerAction;
use Mtolhuys\LaravelSchematics\Actions\Relation\CreatePivotRelationsAction;

trait HasOptionalActions
{
    /**
     * @param $request
     */
    public function optionalActions($request)
    {
        if (! is_array($request['actions'])) {
            return;
        }

        foreach ($request['actions'] as $option => $shouldUse) {
            if (json_decode($shouldUse, false)) {
                $this->getAction($option)->execute($request);
            }
        }
    }

    /**
     * @param $option
     * @return mixed
     */
    private function getAction($option)
    {
        return [
            'hasResourceController' => new CreateResourceControllerAction,
            'hasRelationMigration' => new CreateRelationMigrationAction,
            'hasColumnsMigration' => new CreateColumnsMigrationAction,
            'hasModelMigration' => new CreateModelMigrationAction,
            'deletesMigration' => new DeleteMigrationAction,
			'hasFormRequest' => new CreateFormRequestAction,
			'hasPivotRelations' => new CreatePivotRelationsAction,
        ][$option];
    }
}
