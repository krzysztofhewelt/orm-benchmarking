<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use Faker\Factory;
use Faker\Generator;

class CoursesSeeder
{
    protected Generator $faker;

    public function run(int $coursesCount) : void
    {
        $this->faker = Factory::create('pl_PL');

        Course::truncate();

        $studentIds = Student::all()->pluck('user_id')->toArray();

        for($i = 1; $i <= $coursesCount; $i++) {
            $course = Course::create([
                'name' => $this->faker->sentence(5),
                'description' => $this->faker->text,
                'available_from' => $this->faker->dateTimeBetween('-3 weeks', '+3 weeks'),
                'available_to' => $this->faker->dateTimeBetween('+4 weeks', '12 weeks')
            ]);

            // assign students
            $course->users()->sync($this->faker->randomElements($studentIds, 20));

            // assign teacher
            $course->users()->syncWithoutDetaching([$i]);
        }
    }
}