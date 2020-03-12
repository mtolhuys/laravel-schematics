<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Schematics
    |--------------------------------------------------------------------------
    |
    | Here you may define the namespaces, middleware and toggle auto-migration.
    | Middleware will be applied to the schematic routes and auto-migration
    | will run the up and down methods on edit events through the UI.
    |
    */

    'controller-namespace' => null,
    'form-request-namespace' => 'App\\Http\\Requests',
    'model' => [
        'namespace' => 'App\\',
        'path' => app_path(),
        'paths' => [
            app_path(),
        ],
    ],
    'middleware' => null,
    'auto-migrate' => false,

    /*
    |--------------------------------------------------------------------------
    | Create, Update & Delete
    |--------------------------------------------------------------------------
    |
    | Here you may define defaults for the scaffolding. These will set
    | the checkboxes in the forms to be checked or not.
    |
    */

    'create' => [
        'migration' => false,
        'resource-controller' => false,
        'form-request' => false,
    ],

    'update' => [
        'migration' => false,
    ],

    'delete' => [
        'migration' => false,
    ],
];
