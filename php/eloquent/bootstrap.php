<?php

require "vendor/autoload.php";
require_once "../dbCredentialsLoader.php";

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();

try {
    $dbCredentials = dbCredentialsLoader();

    $capsule->addConnection([
        "driver" => $dbCredentials['driver'],
        "host" => $dbCredentials['host'],
        "port" => $dbCredentials['port'],
        "database" => $dbCredentials['database'],
        "username" => $dbCredentials['username'],
        "password" => $dbCredentials['password'],
        'options' => [ PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false ]
    ]);
} catch (ErrorException $e) {
    echo $e;
}

$capsule->setAsGlobal();
$capsule->bootEloquent();
