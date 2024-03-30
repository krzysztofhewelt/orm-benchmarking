<?php
declare(strict_types=1);

require "bootstrap.php";
require "../ResultsManager.php";

use App\Entities\User;
use App\Entities\Course;
use Faker\Factory;
use Illuminate\Database\Capsule\Manager as DB;

class Benchmark
{
    const NUMBER_OF_REPEATS = 100;
    const NUMBER_OF_RECORDS = [1, 10];
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
        $this->randomUsers = $this->generateRandomUsers(self::NUMBER_OF_RECORDS[count(self::NUMBER_OF_RECORDS)-1]);

        $this->run('insertUsers', typeOfBenchmark: 'modify');
        $this->run('insertCourses', typeOfBenchmark: 'modify');

        $this->run('updateUsers', typeOfBenchmark: 'modify');
        $this->run('updateCourses', typeOfBenchmark: 'modify');



        $this->saveResultsData();
    }

    public function run(string $method, int $times = self::NUMBER_OF_REPEATS, array $numberOfRecords = self::NUMBER_OF_RECORDS, string $typeOfBenchmark = ''): void
    {
        echo sprintf("avg time of %s:\n", $method);

        $benchmarkNumberOfRecords = array();
        foreach($numberOfRecords as $recordsToFetch) {
            $tempTimes = array();
            $methodArguments = ($typeOfBenchmark === 'update') ? $this->randomUsers : $recordsToFetch;

            for ($i = 0; $i < $times; $i++) {
                $start = microtime(true);
                $this->$method($methodArguments);
                $tempTimes[] = microtime(true) - $start;

                if($typeOfBenchmark === 'update') {
                    $this->deleteLastNUsers(10);
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

            $this->deleteLastNUsers(10);

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
                "orm_name" => "Eloquent",
                "orm_version" => "11.1.1",
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
        return User::with(['teacher', 'student'])->limit($quantity)->get();
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


    private function generateRandomUsers(int $quantity) : array
    {
        $users = array();
        $faker = Factory::create('pl_PL');

        for($i = 0; $i < $quantity; $i++) {
            $users[] = [
                'name' => $faker->firstName,
                'surname' => $faker->lastName,
                'email' => 'email_new_' . $i+1 . '@email.com',
                'account_role' => $faker->randomElement(['student', 'teacher']),
                'password' => 'User#12345',
                'active' => 1
            ];
        }

        return $users;
    }

    private function generateRandomCourses(int $quantity) : array
    {
        $courses = array();
        $faker = Factory::create('pl_PL');

        for($i = 0; $i < $quantity; $i++) {
            $courses[] = [
                'name' => $faker->sentence(5),
                'description' => $faker->text,
                'available_from' => $faker->dateTimeBetween('-3 weeks', '+3 weeks'),
                'available_to' => $faker->dateTimeBetween('+4 weeks', '12 weeks')
            ];
        }

        return $courses;
    }

    private function deleteLastNUsers(int $quantity) : void
    {
        User::orderBy('id', 'desc')->take($quantity)->delete();
    }
}

new Benchmark();