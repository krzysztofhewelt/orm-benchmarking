<?php

return [
    'paths' => [
        'migrations' => 'database\migrations'
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'dev' => [
            'adapter' => 'pgsql',
            'host' => 'localhost',
            'name' => 'orm_benchmarking',
            'user' => 'postgres',
            'pass' => 'superpassword',
            'port' => '5432'
        ]
    ]
];