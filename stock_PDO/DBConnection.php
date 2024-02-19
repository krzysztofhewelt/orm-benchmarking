<?php

class DBConnection extends PDO
{
    public function __construct(string $host, string $port, string $database, string $username, string $password)
    {
        parent::__construct("pgsql:host=$host;port=$port;dbname=$database", $username, $password);
        $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
}
