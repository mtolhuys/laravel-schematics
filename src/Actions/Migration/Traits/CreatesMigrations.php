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

        switch (strtolower($type)) {
            case 'date':
                $type = 'date';
                break;
            case 'timestamp':
                $type = 'dateTime';
                break;
            case 'int':
            case 'tinyint':
            case 'bigint':
                $type = 'integer';
                break;
            case 'varchar':
            case 'text':
            default:
                $type = 'string';
        }

        if ($max > 0) {
            $type .= "|max:{$max}";
        }

        return $type;
    }

}
