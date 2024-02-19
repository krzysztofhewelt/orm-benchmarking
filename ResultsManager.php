<?php
declare(strict_types=1);

class ResultsManager
{
    const RESULTS_FILE = __DIR__ . "/results.json";

    public static function saveResultToFile(mixed $jsonData): bool
    {
        if (!self::validateBodyData($jsonData)) {
            echo "Given data is not valid!";
            return false;
        }

        if (!file_exists(self::RESULTS_FILE))
            touch(self::RESULTS_FILE);

        $fileData = file_get_contents(self::RESULTS_FILE);

        $result = [
            'orm_name' => $jsonData->orm_name,
            'orm_version' => $jsonData->orm_version,
            'benchmark_date' => date("d.m.Y H:i:s"),
            'benchmarks' => $jsonData->benchmarks
        ];

        $jsonFileData = json_decode($fileData, true);
        $jsonFileData[] = $result;

        return file_put_contents(self::RESULTS_FILE, json_encode($jsonFileData)) !== false;
    }

    public static function validateBodyData(mixed $jsonData): bool
    {
        // orm_name
        if (!isset($jsonData->orm_name) || !is_string($jsonData->orm_name))
            return false;

        // orm_version
        if (!isset($jsonData->orm_version) || !is_string($jsonData->orm_version))
            return false;

        // benchmarks
        if (!isset($jsonData->benchmarks))
            return false;

        foreach ($jsonData->benchmarks as $benchmark) {
            $benchmark = (object) $benchmark; // clean way to convert assoc array to object (without json_encode(json_decode))

            if ((!property_exists($benchmark, "name") || !is_string($benchmark->name))
                || (!property_exists($benchmark, "time") || !is_numeric($benchmark->time))
                || (!property_exists($benchmark, "queries")) || !is_array($benchmark->queries))
                return false;
        }

        return true;
    }
}