<?php

return [
    'paths' => [
        'migrations' => 'database\migrations'
    ],
    'migration_base_class' => '\MyProject\Database\Migration\Migration',
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'dev',
        'dev' => [
            'adapter' => 'pgsql',
            'host' => 'localhost',
            'name' => 'orm_benchmarking',
            'user' => 'postgres',
            'pass' => 'admin123',
            'port' => '5432'
        ]
    ]
];