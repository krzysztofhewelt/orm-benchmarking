<?php
declare(strict_types=1);

class ResultsManager
{
    const RESULTS_FILE = __DIR__ . "/results.json";

    public static function saveResultToFile(mixed $jsonData, bool $override = false): bool
    {
        if (!self::validateBodyData($jsonData)) {
            echo "Given data is not valid!";
            return false;
        }

        $fileExists = file_exists(self::RESULTS_FILE);
        if ($override && $fileExists) {
            if (!unlink(self::RESULTS_FILE)) {
                echo "Cannot delete results file!";
                return false;
            }
        }

        $fileExists = file_exists(self::RESULTS_FILE);
        if (!$fileExists) {
            if (!touch(self::RESULTS_FILE)) {
                echo "Cannot create results file!";
                return false;
            }
        }

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
            $benchmark = (object)$benchmark; // clean way to convert assoc array to object (without json_encode(json_decode))

            if ((!property_exists($benchmark, "name") || !is_string($benchmark->name)))
                return false;

            if ((!property_exists($benchmark, "numberOfRecords") || !is_array($benchmark->numberOfRecords)))
                return false;

            foreach ($benchmark->numberOfRecords as $benchmarkNumberOfRecords) {
                $benchmarkNumberOfRecords = (object)$benchmarkNumberOfRecords;

                if ((!property_exists($benchmarkNumberOfRecords, "time") || !is_numeric($benchmarkNumberOfRecords->time))
                    || (!property_exists($benchmarkNumberOfRecords, "min") || !is_numeric($benchmarkNumberOfRecords->min))
                    || (!property_exists($benchmarkNumberOfRecords, "max") || !is_numeric($benchmarkNumberOfRecords->max))
                    || (!property_exists($benchmarkNumberOfRecords, "numberOfQueries") || !is_numeric($benchmarkNumberOfRecords->numberOfQueries))
                    || (!property_exists($benchmarkNumberOfRecords, "queries") || !is_array($benchmarkNumberOfRecords->queries)))
                    return false;
            }
        }

        return true;
    }
}