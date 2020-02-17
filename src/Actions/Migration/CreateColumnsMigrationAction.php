<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Services\RuleParser;
use Mtolhuys\LaravelSchematics\Actions\Migration\Traits\CreatesMigrations;

class CreateColumnsMigrationAction
{
    use CreatesMigrations;

    /**
     * @param $request
     */
    public function execute($request)
    {
        $model = $request['model'];
        $table = app($model)->getTable();
        $stub = __DIR__ . '/../../../resources/stubs/migration/columns.stub';
        $title = $this->title($request, $table);
        $this->filename = 'database/migrations/' . date('Y_m_d_His') . '_' . Str::snake($title) . '.php';
        $request = $this->separateNameAndTypeChanges($request);

        File::put(base_path($this->filename), str_replace([
            '$classname$',
            '$table$',
            '$columnsUp$',
            '$columnsDown$'
        ], [
            $title,
            $table,
            rtrim($this->getUpMethods($request)),
            rtrim($this->getDownMethods($request)),
        ], File::get($stub)));
    }

    /**
     * Combine and return all up methods
     *
     * @param $request
     * @return string
     */
    private function getUpMethods($request): string
    {
        $changes = array_filter(
            $this->changes($request['fields'], true)
        );

        return (empty($changes) ? '' : RuleParser::fieldsToMigrationMethods($this->getFields($changes)))
            . (empty($request['created']) ? '' : RuleParser::fieldsToMigrationMethods(
                $this->getFields($request['created'])
            ))
            . (empty($request['deleted']) ? '' : RuleParser::fieldsToMigrationMethods(
                $this->getFields($this->deleted($request['deleted']))
            ));
    }

    /**
     * Combine and return all down methods
     *
     * @param $request
     * @return string
     */
    private function getDownMethods($request): string
    {
        $changes = array_filter(
            $this->changes(array_reverse($request['fields']), false)
        );

        return
            (empty($changes) ? '' : RuleParser::fieldsToMigrationMethods($this->getFields($changes)))
            . (empty($request['deleted']) ? '' : RuleParser::fieldsToMigrationMethods(
                $this->getFields(array_map(function ($field) {
                    $field['type'] = $this->parseColumnType($field['columnType']);

                    return $field;
                }, $request['deleted']))
            ))
            . (empty($request['created']) ? '' : RuleParser::fieldsToMigrationMethods(
                $this->getFields(
                    $this->deleted($request['created']))
            ));
    }

    /**
     * Add change specific type fields
     *
     * @param $fields
     * @param bool $up
     * @return array
     */
    private function changes($fields, bool $up): array
    {
        return array_map(function ($field) use ($up) {
            $changedField = json_decode($field['exists'], false)
                && json_decode($field['changed'], false);

            if ($changedField) {
                $field = $up ? $this->setUpChanges($field)
                    : $field = $this->setDownChanges($field);

                return $field;
            }
        }, $fields);
    }

    /**
     * @param $field
     * @return mixed
     */
    private function setUpChanges($field)
    {
        if (isset($field['from'])) {
            $field['type'] = "renameColumn|from:{$field['from']}|required";
        } else {
            $field['type'] .= '|change';
        }

        return $field;
    }

    /**
     * @param $field
     * @return mixed
     */
    private function setDownChanges($field)
    {
        if (isset($field['from'])) {
            $field['type'] = "renameColumn|from:{$field['to']}|required";
            $field['name'] = $field['from'];
        } else {
            $field['type'] = $this->parseColumnType($field['columnType']). '|change';
        }

        return $field;
    }

    /**
     * Add dropColumn for deleted fields
     *
     * @param $deleted
     * @return array
     */
    private function deleted($deleted): array
    {
        return array_map(static function ($field) {
            $field['type'] = 'dropColumn|required';

            return $field;
        }, $deleted);
    }

    /**
     * Get array of field names
     *
     * @param $request
     * @param string $table
     * @return string
     */
    private function title($request, string $table): string
    {
        $actions = static function ($action) use ($request) {
            if (isset($request[$action])) {
                return ucfirst($action)
                    . ucfirst(Str::camel(
                        implode('_and_', array_map(static function ($field) {
                            return empty($field['from']) ? $field['name'] : $field['from'];
                        }, array_merge(
                            $request[$action]
                        )))
                    ));
            }
        };

        return implode('', array_filter([
                $actions('created'),
                $actions('changed'),
                $actions('deleted')
            ])) . 'ColumnIn' . ucfirst(Str::camel($table)) . 'Table';
    }

    private function separateNameAndTypeChanges($request)
    {
        if (empty($request['changed'])) {
            return $request;
        }

        $changes = array_filter(array_map(static function($field) {
            if (!empty($field['from']) && !empty($field['type'])) {
                $field['name'] = $field['from'];
                $field['changed'] = true;
                $field['from'] = null;

                return $field;
            }
        }, $request['changed']));

        if (! empty($changes)) {
            $request['fields'] = array_merge([$changes[0]], $request['fields']);
        }

        return $request;
    }
}
