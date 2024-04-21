<?php
declare(strict_types=1);

require "../eloquent/RandomUsersGenerator.php";
require "../eloquent/RandomCoursesGenerator.php";
require "../../ResultsManager.php";
require_once "DBConnection.php";
require_once "../dbCredentialsLoader.php";
require_once "../benchmarkUtils.php";

class Benchmark
{
    const NUMBER_OF_REPEATS = 100;
    const NUMBER_OF_RECORDS = [1, 50, 100, 500, 1000];
    private RandomUsersGenerator $randomUsersGenerator;
    private RandomCoursesGenerator $randomCoursesGenerator;

    private DBConnection $dbConnection;

    public array $benchmarks;

    public function __construct()
    {
        $this->randomUsersGenerator = new RandomUsersGenerator(1000, false);
        $this->randomCoursesGenerator = new RandomCoursesGenerator(1000, false);

        if (!$this->initializeDatabaseConnection())
            exit('Cannot initialize database connection');

        $this->run('selectSimpleUsers', typeOfBenchmark: 'select');
        $this->run('selectComplexStudentsWithInformationAndCourses', typeOfBenchmark: 'select');
        $this->run('selectComplexUsersTasks', typeOfBenchmark: 'select');

        $this->run('insertUsers', typeOfBenchmark: 'insert', table: 'users');
        $this->run('insertCourses', typeOfBenchmark: 'insert', table: 'courses');

        $this->run('updateCoursesEndDate', typeOfBenchmark: 'update');

        $this->run('detachUsersFromCourses', typeOfBenchmark: 'delete');
        $this->run('deleteCourses', typeOfBenchmark: 'delete');

        $this->saveResultsData();
    }

    private function initializeDatabaseConnection(): bool
    {
        try {
            $dbCredentials = dbCredentialsLoader();
            $this->dbConnection = new DBConnection($dbCredentials['host'], $dbCredentials['port'], $dbCredentials['database'], $dbCredentials['username'], $dbCredentials['password']);
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function run(string $method, int $times = self::NUMBER_OF_REPEATS, array $numberOfRecords = self::NUMBER_OF_RECORDS, string $typeOfBenchmark = '', string $table = ''): void
    {
        echo sprintf("avg time of %s:\n", $method);

        $benchmarkNumberOfRecords = array();
        foreach ($numberOfRecords as $recordsToFetch) {
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

                if ($typeOfBenchmark !== 'select')
                    restoreDatabase();
            }

            $avgTime = (array_sum($tempTimes) / count($tempTimes)) * 1000;
            $minTime = min($tempTimes) * 1000;
            $maxTime = max($tempTimes) * 1000;

            $benchmarkNumberOfRecords[$recordsToFetch] = [
                'time' => $avgTime,
                'min' => $minTime,
                'max' => $maxTime,
                'numberOfQueries' => 0,
                'queries' => []
            ];

            if ($typeOfBenchmark !== 'select')
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

    public function saveResultsData(): bool
    {
        return ResultsManager::saveResultToFile(
            (object)[
                "orm_name" => "PHP NO-ORM",
                "orm_version" => "8.2",
                "benchmarks" => $this->benchmarks
            ]);
    }


    // Pozostałe metody benchmarka
    private function selectSimpleUsers(int $quantity): mixed
    {
        $statement = $this->dbConnection->prepare("SELECT * FROM users LIMIT :quantity");
        $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function selectComplexStudentsWithInformationAndCourses(int $quantity): mixed
    {
        $statement = $this->dbConnection->prepare("SELECT * FROM (SELECT * FROM users WHERE account_role = 'student' ORDER BY surname LIMIT :quantity) as us INNER JOIN student_info ON us.id = student_info.user_id INNER JOIN orm_benchmarking.course_enrollments ce on us.id = ce.user_id INNER JOIN orm_benchmarking.courses c on ce.course_id = c.id");
        $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function selectComplexUsersTasks(int $quantity) : mixed
    {
        $statement = $this->dbConnection->prepare("SELECT * FROM tasks INNER JOIN orm_benchmarking.courses c on tasks.course_id = c.id INNER JOIN orm_benchmarking.course_enrollments ce on c.id = ce.course_id INNER JOIN (SELECT * FROM users LIMIT :quantity) u on ce.user_id = u.id");
        $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Pozostałe metody select
    private function insertUsers(array $users): void
    {
        $statement = $this->dbConnection->prepare("INSERT INTO users (name, surname, email, password, account_role, active) VALUES (:name, :surname, :email, :password, :account_role, :active)");
        $statementStudent = $this->dbConnection->prepare("INSERT INTO student_info (user_id, field_of_study, semester, year_of_study, mode_of_study) VALUES (:user_id, :field_of_study, :semester, :year_of_study, :mode_of_study)");
        $statementTeacher = $this->dbConnection->prepare("INSERT INTO teacher_info (user_id, scien_degree, business_email, contact_number, room, consultation_hours) VALUES (:user_id, :scien_degree, :business_email, :contact_number, :room, :consultation_hours)");

        foreach ($users as $userData) {
            $this->dbConnection->beginTransaction();

            $statement->bindValue(':name', $userData['name']);
            $statement->bindValue(':surname', $userData['surname']);
            $statement->bindValue(':email', $userData['email']);
            $statement->bindValue(':password', $userData['password']);
            $statement->bindValue(':account_role', $userData['account_role']);
            $statement->bindValue(':active', $userData['active'], PDO::PARAM_BOOL);
            $statement->execute();

            if (isset($userData['student'])) {
                $statementStudent->execute([$this->dbConnection->lastInsertId(), ...$userData['student']]);
            } elseif (isset($userData['teacher'])) {
                $statementTeacher->execute([$this->dbConnection->lastInsertId(), ...$userData['teacher']]);
            }

            $this->dbConnection->commit();
        }
    }

    private function insertCourses(array $courses): void
    {
        $statement = $this->dbConnection->prepare("INSERT INTO courses (name, description, available_from, available_to) VALUES (:name, :description, :available_from, :available_to)");

        foreach ($courses as $courseData) {
            $statement->execute($courseData);
        }
    }

    // Pozostałe metody insert
    private function updateCoursesEndDate(int $quantity) : void
    {
        $statement = $this->dbConnection->prepare("UPDATE courses SET available_to = '2024-10-01' LIMIT :quantity");
        $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $statement->execute();
    }

    // Pozostałe metody update
    private function detachUsersFromCourses(int $quantityUsers): void
    {
        $stmt = $this->dbConnection->prepare("SELECT * FROM users LIMIT :limit");
        $stmt->bindParam(':limit', $quantityUsers, PDO::PARAM_INT);
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user) {
            $userId = $user['id'];

            $stmt = $this->dbConnection->prepare("DELETE FROM course_enrollments WHERE user_id = :userId");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    private function deleteCourses(int $quantity): void
    {
        $statement = $this->dbConnection->prepare("DELETE FROM courses LIMIT :quantity");
        $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $statement->execute();
    }
}

new Benchmark();
