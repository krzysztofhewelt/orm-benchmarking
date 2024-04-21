<?php
declare(strict_types=1);

require "bootstrap.php";
require "../../ResultsManager.php";
require "RandomUsersGenerator.php";
require "RandomCoursesGenerator.php";
require "../benchmarkUtils.php";

use App\Entities\Course;
use App\Entities\User;
use Illuminate\Database\Capsule\Manager as DB;

class Benchmark
{
    const NUMBER_OF_REPEATS = 100;
    const NUMBER_OF_RECORDS = [1, 50, 100, 500, 1000];
    private RandomUsersGenerator $randomUsersGenerator;
    private RandomCoursesGenerator $randomCoursesGenerator;

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
        backupDatabase();
        $this->randomUsersGenerator = new RandomUsersGenerator(1000, false);
        $this->randomCoursesGenerator = new RandomCoursesGenerator(1000, false);

        $this->run('selectSimpleUsers', typeOfBenchmark: 'select');
        $this->run('selectComplexStudentsWithInformationAndCourses', typeOfBenchmark: 'select');
        $this->run('selectComplexUsersTasks', typeOfBenchmark: 'select');

//        $this->run('insertUsers', typeOfBenchmark: 'insert', table: 'users');
//        $this->run('insertCourses', typeOfBenchmark: 'insert', table: 'courses');
//
//        $this->run('updateCoursesEndDate', typeOfBenchmark: 'update');
//
//        $this->run('detachUsersFromCourses', typeOfBenchmark: 'delete');
//        $this->run('deleteCourses', typeOfBenchmark: 'delete');

        $this->saveResultsData();
    }

    public function run(string $method, int $times = self::NUMBER_OF_REPEATS, array $numberOfRecords = self::NUMBER_OF_RECORDS, string $typeOfBenchmark = '', string $table = ''): void
    {
        echo sprintf("avg time of %s:\n", $method);

        $benchmarkNumberOfRecords = array();
        foreach($numberOfRecords as $recordsToFetch) {
            $tempTimes = array();

            $data = [];
            if($table === 'users')
                $data = $this->randomUsersGenerator->getRandomUsers();
            elseif($table === 'courses')
                $data = $this->randomCoursesGenerator->getRandomCourses();

            $methodArguments = getMethodArgumentForMethod($typeOfBenchmark, $table, $recordsToFetch, data: $data);

            for ($i = 0; $i < $times; $i++) {
                $start = microtime(true);
                $this->$method($methodArguments);
                $tempTimes[] = microtime(true) - $start;

                if($typeOfBenchmark !== 'select')
                    restoreDatabase();
            }

            $avgTime = (array_sum($tempTimes) / count($tempTimes)) * 1000;
            $minTime = min($tempTimes) * 1000;
            $maxTime = max($tempTimes) * 1000;

            $generatedQueries = $this->getQueries($method, $methodArguments);
            $numberOfQueries = count($generatedQueries);
            if($typeOfBenchmark !== 'select')
                if(count($generatedQueries) > 10)
                    $generatedQueries = array_slice($generatedQueries, 0, 10);

            $benchmarkNumberOfRecords[$recordsToFetch] = [
                'time' => $avgTime,
                'min' => $minTime,
                'max' => $maxTime,
                'numberOfQueries' => $numberOfQueries,
                'queries' => $generatedQueries
            ];

            if($typeOfBenchmark !== 'select')
                restoreDatabase();

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
            ], true);
    }

    /**
     * ======================
     *     SELECT QUERIES
     * ======================
     */
    private function selectSimpleUsers(int $quantity) : mixed
    {
        return User::limit($quantity)->get();
    }

    private function selectComplexStudentsWithInformationAndCourses(int $quantity) : mixed
    {
        return User::where('account_role', '=', 'student')->with(['student', 'courses'])->orderBy('surname')->limit($quantity)->get();
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
    private function insertUsers(array $users) : void
    {
        foreach ($users as $userData) {
            DB::beginTransaction();
            $user = User::create([
                'name' => $userData['name'],
                'surname' => $userData['surname'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'account_role' => $userData['account_role'],
                'active' => $userData['active']
            ]);

            if(isset($userData['student']))
                $user->student()->create($userData['student']);

            if(isset($userData['courses']))
                $user->courses()->createMany($userData['courses']);

            DB::commit();
        }
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
    function updateCoursesEndDate(int $quantity)
    {
        return Course::take($quantity)->update(['available_to' => '2024-10-01']);
    }

    /**
     * ======================
     *     DELETE QUERIES
     * ======================
     */
    private function detachUsersFromCourses(int $quantityUsers) : mixed
    {
        return User::take($quantityUsers)->get()->each(function ($user) {
            $user->courses()->detach();
        });
    }

    private function deleteCourses(int $quantity) : mixed
    {
        return Course::orderBy('id', 'desc')->take($quantity)->delete();
    }
}

new Benchmark();