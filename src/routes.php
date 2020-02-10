<?php

Route::group([
    'prefix' => 'schematics',
    'namespace' => 'Mtolhuys\LaravelSchematics\Http\Controllers',
    'middleware' => config('schematics.middleware', [])
], static function () {
    Route::get('/', 'SchematicsController@index');
    Route::get('/details/{table}', 'SchematicsController@details');
    Route::get('/clear-cache', 'SchematicsController@clearCache');
    Route::get('/refresh', 'SchematicsController@schematics');

    Route::group(['prefix' => 'relations'], static function () {
        Route::post('/create', 'RelationsController@create');
        Route::post('/delete', 'RelationsController@delete');
    });

    Route::group(['prefix' => 'models'], static function () {
        Route::post('/create', 'ModelsController@create');
        Route::post('/delete', 'ModelsController@delete');
    });

    Route::group(['prefix' => 'migrations'], static function () {
        Route::post('/run', 'MigrationsController@run');
        Route::post('/rollback', 'MigrationsController@rollback');
        Route::post('/refresh', 'MigrationsController@refresh');
        Route::post('/fresh', 'MigrationsController@fresh');
        Route::post('/seed', 'MigrationsController@seed');
    });
});
