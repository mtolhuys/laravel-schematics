<?php

Route::group([
    'prefix' => 'schematics',
    'namespace' => 'Mtolhuys\LaravelSchematics\Http\Controllers',
    'middleware' => config('schematics.middleware', [])
], static function() {
    Route::get('/', 'SchematicsController@index');
    Route::get('/details/{table}', 'SchematicsController@details');
    Route::get('/clear-cache', 'SchematicsController@clearCache');
    Route::get('/refresh', 'SchematicsController@modelsWithRelations');
    Route::post('/new-relation', 'SchematicsController@newRelation');
    Route::post('/remove-relation', 'SchematicsController@removeRelation');
});
