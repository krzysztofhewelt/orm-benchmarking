<?php

/**
 * @throws ErrorException
 */
function dbCredentialsLoader(string $filepath = "../../dbCredentials.json"): array
{
    if(!file_exists($filepath))
        throw new ErrorException("File does not exists");

    $file = file_get_contents($filepath);
    $jsonFile = json_decode($file, true);

    if (!isset($jsonFile['driver']) ||
        !isset($jsonFile['host']) ||
        !isset($jsonFile['port']) ||
        !isset($jsonFile['database']) ||
        !isset($jsonFile['username']) ||
        !isset($jsonFile['password'])) {
        throw new ErrorException("Not valid credentials");
    }

    if($jsonFile['driver'] != 'pgsql')
        throw new ErrorException('Available driver: pgsql');


    return $jsonFile;
}