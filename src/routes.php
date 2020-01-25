<?php

Route::group(["namespace" => "Mtolhuys\LaravelSchematics\Http\Controllers"],function() {
    Route::get('/schematics', 'SchematicsController@index');
    Route::get('/schematics/clear-cache', 'SchematicsController@clearCache');
});
