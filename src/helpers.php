<?php

use Mtolhuys\LaravelSchematics\Services\ModelMapper;
use Mtolhuys\LaravelSchematics\Services\RelationMapper;

if (!function_exists('models')) {
    /**
     * Get subclasses of Illuminate\Database\Eloquent\Model
     *
     * @return array
     * @throws ReflectionException
     */
    function models()
    {
        return ModelMapper::map();
    }
}


if (!function_exists('relations')) {
    /**
     * Get relations details map through array of Eloquent models
     *
     * @param null $models
     *
     * @return array
     * @throws ReflectionException
     */
    function relations($models = null)
    {
        return RelationMapper::map($models);
    }
}
