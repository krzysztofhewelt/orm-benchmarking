<?php
declare(strict_types=1);

require "bootstrap.php";
require "../../ResultsManager.php";
require "BenchmarkUtils.php";

use App\Entities\Course;
use App\Entities\User;
use Illuminate\Database\Capsule\Manager as DB;

class Benchmark
{
    const NUMBER_OF_REPEATS = 100;
    const NUMBER_OF_RECORDS = [1, 50, 100, 1000];
    private array $randomUsers;

    /**
     * Contains all benchmarks results.
     *
     * @var array<int, array{
     *     time: float,
     *     min: float,
     *     max: float,
     *     queries: array<string>
     * }> $benchmarks
     */
    public array $benchmarks;

    public function __construct()
    {
        $this->run('selectSimpleUsers');
        $this->run('selectComplexUsersWithInformation');
        $this->run('selectComplexUsersTasks');
        $this->randomUsers = generateRandomUsers(self::NUMBER_OF_RECORDS[count(self::NUMBER_OF_RECORDS)-1]);

        $this->run('insertUsers', typeOfBenchmark: 'create');
//        $this->run('insertCourses', typeOfBenchmark: 'create');
//
//        // only one record to update
//        $this->run('updateUsers', typeOfBenchmark: 'update');
//        $this->run('updateCourses', typeOfBenchmark: 'update');
//
//        $this->run('deleteUsers', typeOfBenchmark: 'delete');
//        $this->run('deleteCourses', typeOfBenchmark: 'delete');

        $this->saveResultsData();
    }

    public function run(string $method, int $times = self::NUMBER_OF_REPEATS, array $numberOfRecords = self::NUMBER_OF_RECORDS, string $typeOfBenchmark = ''): void
    {
        echo sprintf("avg time of %s:\n", $method);

        $benchmarkNumberOfRecords = array();
        foreach($numberOfRecords as $recordsToFetch) {
            $tempTimes = array();
//            $methodArguments = ($typeOfBenchmark === 'update') ? $this->randomUsers : $recordsToFetch;
            $methodArguments = ($typeOfBenchmark === 'create') ? array_slice($this->randomUsers, 0, $recordsToFetch) : $recordsToFetch;

            for ($i = 0; $i < $times; $i++) {
                $start = microtime(true);
                $this->$method($methodArguments);
                $tempTimes[] = microtime(true) - $start;

                if($typeOfBenchmark === 'create') {
                    $this->deleteLastNUsers($recordsToFetch);
                } elseif ($typeOfBenchmark === 'delete') {
                    # add n users
                }
            }

            $avgTime = (array_sum($tempTimes) / count($tempTimes)) * 1000;
            $minTime = min($tempTimes) * 1000;
            $maxTime = max($tempTimes) * 1000;

            $benchmarkNumberOfRecords[$recordsToFetch] = [
                'time' => $avgTime,
                'min' => $minTime,
                'max' => $maxTime,
                'queries' => $this->getQueries($method, $methodArguments)
            ];

            if($typeOfBenchmark === 'create') $this->deleteLastNUsers($recordsToFetch);

            echo sprintf(" - %d: %f; min=%f, max=%f\n", $recordsToFetch, $avgTime, $minTime, $maxTime);
        }

        $this->addBenchmark(
            $method,
            $benchmarkNumberOfRecords
        );
    }

    public function addBenchmark(string $name, array $numberOfRecordsBenchmark): void
    {
        $this->benchmarks[] = [
            'name' => $name,
            'numberOfRecords' => $numberOfRecordsBenchmark
        ];
    }

    public function getQueries($method, $quantity): array
    {
        DB::enableQueryLog();
        DB::flushQueryLog();
        $this->$method($quantity);
        $queries = DB::getQueryLog();

        $transformedQueries = [];
        foreach ($queries as $queryData) {
            $wrappedString = str_replace('?', "'?'", $queryData['query']);
            $transformedQuery = vsprintf(str_replace('?', '%s', $wrappedString), $queryData['bindings']);
            $transformedQueries[] = $transformedQuery;
        }

        DB::disableQueryLog();

        return $transformedQueries;
    }

    public function saveResultsData(): bool
    {
        return ResultsManager::saveResultToFile(
            (object)[
                "orm_name" => "eloquent",
                "orm_version" => \Composer\InstalledVersions::getVersion('illuminate/database'),
                "benchmarks" => $this->benchmarks
            ]);
    }

    /**
     * ======================
     *     SELECT QUERIES
     * ======================
     */

    // something else with WHERE clause

    private function selectSimpleUsers(int $quantity) : mixed
    {
        return User::limit($quantity)->get();
    }

    private function selectComplexUsersWithInformation(int $quantity) : mixed
    {
        return User::where('account_role', '=', 'student')->with(['student'])->orderBy('surname', 'asc')->limit($quantity)->get();
    }

    private function selectComplexUsersTasks(int $quantity) : mixed
    {
        return User::with('courses.tasks')->limit($quantity)->get();
    }

    /**
     * ======================
     *     INSERT QUERIES
     * ======================
     */
    private function insertUsers(array $users) : mixed
    {
        return User::insert($users);
    }

    private function insertCourses(array $courses) : mixed
    {
        return Course::insert($courses);
    }

    /**
     * ======================
     *     UPDATE QUERIES
     * ======================
     */



    /**
     * ======================
     *     DELETE QUERIES
     * ======================
     */
    private function deleteLastNUsers(int $quantity) : void
    {
        User::orderBy('id', 'desc')->take($quantity)->delete();
    }

    private function deleteLastNCourses(int $quantity) : void
    {
        Course::orderBy('id', 'desc')->take($quantity)->delete();
    }
}

new Benchmark();