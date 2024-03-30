<?php

try {
    $dbCredentials = dbCredentialsLoader();

    return [
        'paths' => [
            'migrations' => 'Database\Migrations'
        ],
        'environments' => [
            'default_migration_table' => 'migrations',
            'production' => [
                'adapter' => $dbCredentials['driver'],
                'host' => $dbCredentials['host'],
                'port' => $dbCredentials['port'],
                'name' => $dbCredentials['database'],
                'user' => $dbCredentials['username'],
                'pass' => $dbCredentials['password']
            ]
        ]
    ];
} catch (ErrorException $e) {
    echo $e;
}
