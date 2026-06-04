<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    */
    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    */
    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

    /*
    |--------------------------------------------------------------------------
    | Blade Directives
    |--------------------------------------------------------------------------
    */
    'blade' => [
        'if' => [
            'auth' => \App\Blade\AuthDirectives::class,
            'admin' => \App\Blade\AdminDirectives::class,
            'customer' => \App\Blade\CustomerDirectives::class,
        ],
    ],

];
