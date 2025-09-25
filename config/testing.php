<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Test Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration specific to testing environment.
    | Values here will override the main configuration when running tests.
    |
    */

    'database' => [
        'default' => 'mysql_testing',
        'connections' => [
            'mysql_testing' => [
                'driver' => 'mysql',
                'host' => env('DB_TEST_HOST', '127.0.0.1'),
                'port' => env('DB_TEST_PORT', '3306'),
                'database' => env('DB_TEST_DATABASE', 'etqan_testing'),
                'username' => env('DB_TEST_USERNAME', 'root'),
                'password' => env('DB_TEST_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ],
        ],
    ],

    'cache' => [
        'default' => 'array',
    ],

    'session' => [
        'driver' => 'array',
    ],

    'queue' => [
        'default' => 'sync',
    ],

    'mail' => [
        'default' => 'array',
    ],
];
