<?php

require "../../bootstrap.php";

use App\User;
use Database\Seeders\UsersSeeder;
use Illuminate\Database\Capsule\Manager as DB;

class DatabaseSeeder extends \Illuminate\Database\Seeder
{
    public function __construct(int $usersCount)
    {
        $this->cleanupDatabase();

        $this->migrate();

        echo "Database seeding! Please wait!\n";

        $time = microtime(true);
        $this->seedDatabase($usersCount);

        echo "Database seeded! Took " . Utils::formatTime(microtime(true) - $time) . " minutes.";
    }

    protected function migrate() {

    }

    protected function seedDatabase(int $usersCount)
    {
       // $faker = Factory::create('pl_PL');

       /* for ($i = 0; $i < $usersCount; $i++)
            User::create([
                "name" => $faker->firstName(),
                "surname" => $faker->lastName(),
                "email" => $faker->unique()->email(),
                "password" => $faker->password(8),
                "account_role" => "student"
            ]);*/

        // for dla drugiej tabeli

        // for dla trzeciej tabeli
        $users = new UsersSeeder();
        $users->run(1000);
        DB::enableQueryLog();
        echo (new User())->where('id', '10')->with('courses.tasks')->get();
        print_r(DB::getQueryLog());
    }
}

$dbSeeder = new DatabaseSeeder(1000);