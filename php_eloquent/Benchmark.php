<?php
declare(strict_types=1);

require "bootstrap.php";
require "../ResultsManager.php";

use App\Entities\User;
use Illuminate\Database\Capsule\Manager as DB;

class Benchmark
{
    const NUMBER_OF_REPEATS = 100;
    const NUMBER_OF_RECORDS = [1, 100, 1000, 5000];
    public array $benchmarks;

    public function __construct()
    {
        $this->run('test1');
//        $this->run('test2', 10);
        $this->saveResultsData();
    }

    public function run(string $method, int $times = self::NUMBER_OF_REPEATS): void
    {
        $tempTimes = array();

        for ($i = 0; $i < $times; $i++) {
            $start = microtime(true);
            $this->$method();
            $tempTimes[] = microtime(true) - $start;
        }

        $avgTime = (array_sum($tempTimes) / count($tempTimes)) * 1000;
        $minTime = min($tempTimes) * 1000;
        $maxTime = max($tempTimes) * 1000;

        $this->addBenchmark(
            $method,
            $avgTime,
            $minTime,
            $maxTime,
            $this->getQueries($method)
        );

        echo sprintf("\navg time of %s: %f; min=%f, max=%f", $method, $avgTime, $minTime, $maxTime);
    }

    public function addBenchmark(string $name, float|int $time, float|int $minTime, float|int $maxTime, array $queries): void
    {
        $this->benchmarks[] = [
            'name' => $name,
            'time' => $time,
            'min' => $minTime,
            'max' => $maxTime,
            'queries' => $queries
        ];
    }

    public function getQueries($method): array
    {
        DB::enableQueryLog();
        $this->$method();
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        return array_map(fn($query) => $query['query'], $queries);
    }

    /*
     * Runs complex select query with nested relations
     */
    public function saveResultsData(): bool
    {
        return ResultsManager::saveResultToFile(
            (object)[
                "orm_name" => "Eloquent",
                "orm_version" => "10.15.0",
                "benchmarks" => $this->benchmarks
            ]);
    }

    /*
     * Inserts some users
     */

    /**
     * Runs simple select query
     *
     * @param $times
     * @return void
     */
    public function test1() : mixed
    {
        return User::where('account_role', 'student')->with('student', 'teacher', 'courses')->first();
    }

    /*
     * Assigns users to courses
     */


    /*
     * Updates some courses
     */

    /*
     * deletes some
     */

    /**
     * Runs complex select query
     */
    public function test2()
    {
        return User::where('id', '>', 6)->get();
        //return User::with('courses.tasks')->get();
    }

    public function test3()
    {
        return User::with('courses.tasks')->first();
    }

    public function test4()
    {

    }
}

new Benchmark();