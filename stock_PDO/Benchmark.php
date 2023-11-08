<?php

require_once "../ResultsManager.php";

class Benchmark
{
    const NUMBER_OF_REPEATS = 100;
    public array $benchmarks;

    public function __construct()
    {
        $this->run('test1');
        $this->saveResultsData();
    }

    public function addBenchmark(string $name, float|int $time, array $queries): void
    {
        $this->benchmarks[] = [
            'name' => $name,
            'time' => $time,
            'queries' => $queries
        ];
    }

    public function run(string $method, int $times = self::NUMBER_OF_REPEATS): void
    {
        $tempTimes = array();

        for ($i = 0; $i < $times; $i++) {
            $start = microtime(true);
            $this->$method();
            $tempTimes[] = microtime(true) - $start;
        }

        $this->addBenchmark(
            $method,
            (array_sum($tempTimes) / count($tempTimes)) * 1000,
            ""
        );

        echo "\navg time of $method: " . (array_sum($tempTimes) / count($tempTimes)) * 1000;
    }

    public function saveResultsData(): bool
    {
        return ResultsManager::saveResultToFile(
            (object)[
                "orm_name" => "PDO SQL",
                "orm_version" => "8.2",
                "benchmarks" => $this->benchmarks
            ]);
    }
}

new Benchmark();