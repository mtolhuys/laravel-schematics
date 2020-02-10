<?php

return [
    'project-name' => null,
    'middleware' => null,
    'auto-migrate' => true,
    'namespace' => 'App\\Models\\',

    /* Decides if relation generator uses \App\Model::class or 'App\Model' for generating relations
     * Possible options: 'string', 'class'
     */
    'relation-generator-method' => 'string',
];
