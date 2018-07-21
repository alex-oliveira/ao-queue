<?php

return [

    'db' => [
        'connection' => env('DB_CONNECTION', 'mysql'),
        'tables' => [
            'types' => 'ao_queue__types',
            'tasks' => 'ao_queue__tasks'
        ]
    ],

    'screens' => [
        'namespace' => 'ao-queue'
    ],

    'master' => [
        'seconds_between_requests' => 5
    ],

    'routes' => [
        'config' => [
            'namespace' => 'AoQueue\Controllers',
            'prefix' => 'ao-queue',
            'middleware' => ['api']
        ]
    ],

];
