<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Task;
use Faker\Factory;
use Faker\Generator;

class TasksSeeder
{
    protected Generator $faker;

    public function run($coursesCount) : void
    {
        $this->faker = Factory::create('pl_PL');

        Task::truncate();

        for($i = 1; $i <= $coursesCount; $i++) {
           for($j = 0; $j < $this->faker->numberBetween(5, 10); $j++) {
              Task::create([
                  'name' => $this->faker->sentence(5),
                  'description' => $this->faker->text,
                  'available_from' => $this->faker->dateTimeBetween('-3 weeks', '+3 weeks'),
                  'available_to' => $this->faker->dateTimeBetween('+4 weeks', '12 weeks'),
                  'max_points' => $this->faker->numberBetween(1, 20),
                  'course_id' => $i
              ]);
           }
        }
    }
}