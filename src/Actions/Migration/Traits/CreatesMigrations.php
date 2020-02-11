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
        $this->path = base_path('database/migrations/');
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
        return array_merge(
            ...array_values(array_map(static function ($field) {
                return [
                    $field['name'] => empty($field['type']) ? 'string|max:255' : $field['type']
                ];
            }, $fields))
        );
    }
}
