<?php

class DBConnection extends PDO
{
    private string $host = "localhost";
    private string $database = "orm_benchmarking";
    private string $user = "postgres";
    private string $password = "superpassword";

    public function __construct()
    {
        parent::__construct("pgsql:host=$this->host;dbname=$this->database", $this->user, $this->password);
        $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
}
