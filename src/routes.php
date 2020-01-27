<?php

Route::group([
    'namespace' => 'Mtolhuys\LaravelSchematics\Http\Controllers',
    'middleware' => config('schematics.middleware', [])
], static function() {
    Route::get('/schematics', 'SchematicsController@index');
    Route::get('/schematics/details/{table}', 'SchematicsController@details');
    Route::get('/schematics/clear-cache', 'SchematicsController@clearCache');
});
