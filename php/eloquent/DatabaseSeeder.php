<?php
declare(strict_types=1);

use Database\Seeders\CoursesSeeder;
use Database\Seeders\TasksSeeder;
use Database\Seeders\UsersSeeder;
use Illuminate\Database\Capsule\Manager as DB;

require "bootstrap.php";
require "configurator.php";

class DatabaseSeeder
{
    public function __construct(int $usersCount, int $coursesCount, int $tasksCount)
    {
        $this->printSeedingMessage($usersCount);

        $time = microtime(true);
        $this->seedDatabase($usersCount, $coursesCount, $tasksCount);
        $elapsedTime = round(microtime(true) - $time, 2);

        $this->printSeedingSuccessMessage($elapsedTime);
    }

    protected function seedDatabase(int $usersCount, int $coursesCount, int $tasksCount) : void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // users
        $users = new UsersSeeder();
        $users->run($usersCount);

        // courses and users assign
        $courses = new CoursesSeeder();
        $courses->run($coursesCount);

        // tasks for courses
        $tasks = new TasksSeeder();
        $tasks->run($tasksCount);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    protected function printSeedingMessage(int $usersCount) : void {
        echo "\n\033[32mDatabase seeding with " . $usersCount . " users! Please wait!\033[0m\n";
    }

    protected function printSeedingSuccessMessage(float $elapsedTime) : void {
        echo "\033[33mDatabase seeded! Took " . $elapsedTime . " seconds.\033[0m\n";
    }
}

new DatabaseSeeder($USERS_TO_GENERATE, $COURSES_TO_GENERATE, $TASKS_TO_GENERATE);