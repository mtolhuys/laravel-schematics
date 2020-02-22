<?php

namespace Mtolhuys\LaravelSchematics\Actions\Migration\Traits;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait CreatesMigrations
{
    public
        $autoMigrate,
        $filename,
        $path;

    public function __construct()
    {
        $this->autoMigrate = config('schematics.auto-migrate');
        $this->path = database_path('migrations');
    }

    public function __destruct()
    {
        if ($this->autoMigrate) {
            try {
                Artisan::call('migrate');
            } catch (\Throwable $e) {
                Cache::put('schematics-exception', [
                    'title' => get_class($e),
                    'message' => $e->getMessage(),
                ], 1440);
            }
        }
    }

    /**
     * @param $request
     * @return string
     */
    protected function getLocalKey($request): string
    {
        return empty($request['method']['localKey'])
            ? 'id' : $request['method']['localKey'];
    }

    /**
     * @param $request
     * @return string
     */
    protected function getForeignKey($request): string
    {
        return empty($request['method']['foreignKey'])
            ? strtolower(
                Str::snake(
                    substr(strrchr($request['target'], "\\"), 1) . '_id'
                )
            )
            : $request['method']['foreignKey'];
    }

    /**
     * @param $fields
     * @return array
     */
    protected function getFields($fields): array
    {
        return array_map(static function ($field) {
            return [
                $field['name'] => empty($field['type']) ? 'string|max:255' : $field['type']
            ];
        }, $fields);
    }

    /**
     * @param $field
     * @return mixed
     */
    protected function setUpChange($field)
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
    protected function setDownChange($field)
    {
        if (isset($field['from'])) {
            $field['type'] = "renameColumn|from:{$field['to']}|required";
            $field['name'] = $field['from'];
        } else {
            if (isset($field['to'])) {
                $field['name'] = $field['to'];
            }

            $field['type'] = $this->parseColumnType($field['columnType']) . '|change';
        }

        return $field;
    }


    /**
     * Parsing column type to migration rule
     *
     * @param $columnType
     * @return string
     */
    protected function parseColumnType($columnType): string
    {
        $max = (int)preg_replace('/\D/', '', $columnType);
        $type = str_replace(' ', '',
            preg_replace(
                '/[^a-zA-Z]+/',
                '',
                explode(' ', $columnType, 2)[0]
            )
        );

        $type = key(array_filter([
            'date' => [
                'date'
            ],
            'dateTime' => [
                'timestamp'
            ],
            'string' => [
                'varchar',
                'text'
            ],
            'integer' => [
                'int',
                'bigint',
                'tinyint'
            ],
        ], static function($values) use($type) {
            return in_array(strtolower($type), $values, true);
        }, ARRAY_FILTER_USE_BOTH));

        if ($max > 0) {
            $type .= "|max:{$max}";
        }

        return $type ?? 'string';
    }

}
