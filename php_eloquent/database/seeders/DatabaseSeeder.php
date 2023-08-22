<?php
declare(strict_types=1);

namespace Database\Seeders;

require_once "../../bootstrap.php";

class DatabaseSeeder
{
    public function __construct(int $usersCount)
    {
        echo "Database seeding! Please wait!\n";

        $time = microtime(true);
        $this->seedDatabase($usersCount);

        echo "Database seeded! Took " . microtime(true) - $time . " minutes.";
    }

    protected function seedDatabase(int $usersCount) : void
    {
        // users
        $users = new UsersSeeder();
        $users->run($usersCount);

        // courses and users assign
        $courses = new CoursesSeeder();
        $courses->run((int) floor($usersCount * 0.05));

        // tasks for courses
        $tasks = new TasksSeeder();
        $tasks->run((int) floor($usersCount * 0.05));
    }
}

new DatabaseSeeder(1000);