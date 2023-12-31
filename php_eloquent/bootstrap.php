<?php

require "vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule();

$capsule->addConnection([
    "driver" => "pgsql",
    "host" => "localhost",
    "database" => "orm_benchmarking",
    "username" => "postgres",
    "password" => "superpassword"
]);

$capsule->setAsGlobal();

$capsule->bootEloquent();