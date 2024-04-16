<?php
declare(strict_types=1);

require_once "php/dbCredentialsLoader.php";

try {
    $dbCredentials = dbCredentialsLoader(__DIR__ . '/dbCredentials.json');

    $backupDir = __DIR__ . '/backup.sql';
    $command = "mysqldump --host={$dbCredentials['host']} --port={$dbCredentials['port']} --user={$dbCredentials['username']} --password={$dbCredentials['password']} {$dbCredentials['database']} -r {$backupDir} 2>&1";
    exec($command);
} catch (Exception $e) {
    echo $e->getMessage();
}
