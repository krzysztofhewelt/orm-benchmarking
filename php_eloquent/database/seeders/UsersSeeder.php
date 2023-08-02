<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Teacher;
use App\User;
use App\Student;
use Faker\Factory;
use Illuminate\Database\Seeder;

class UsersSeeder
{
    protected $faker;

    public function run(int $usersCount)
    {
        $this->faker = Factory::create('pl_PL');

        // 5% - teachers
        // remain - students
        // each student and teacher have own information
        // 25% courses
        // course have from 1-5 tasks

        // calc: 10 000 users
        // teachers: 500
        // students: 9500
        // courses: same as teachers (500)
        // users in course: 20

        $teachers = floor($usersCount * 0.05);
        $students = $usersCount - $teachers;

        User::truncate();
        Teacher::truncate();
        Student::truncate();

        for ($i = 0; $i < $teachers; $i++) {
            $user = User::create(
                [
                    'name' => $this->faker->firstName,
                    'surname' => $this->faker->lastName,
                    'email' => $this->faker->unique()->safeEmail,
                    'account_role' => 'teacher',
                    'password' => 'User#12345'
                ]);

            Teacher::create([
                    'user_ID' => $user->id,
                    'scien_degree' => $this->faker->randomElement([
                        'dr',
                        'mgr',
                        'prof.',
                        'dr hab.',
                        'inż.',
                        'lic.',
                    ]),
                    'business_email' => $this->faker->unique()->safeEmail,
                    'contact_number' => $this->faker->phoneNumber,
                    'room' => $this->faker->randomLetter . '-' . $this->faker->randomNumber(3),
                    'consultation_hours' =>
                        $this->faker->dayOfWeek . ' ' . $this->faker->numberBetween(9, 15) . ':00',
                ]);
            }

        for($i = 0; $i < $students; $i++) {
            $user = User::create(
                [
                    'name' => $this->faker->firstName,
                    'surname' => $this->faker->lastName,
                    'email' => $this->faker->unique()->safeEmail,
                    'account_role' => 'student',
                    'password' => 'User#12345'
                ]);

            $year = $this->faker->numberBetween(2018, 2022);

            Student::create([
                'user_ID' => $user->id,
                'field_of_study' => $this->faker->randomElement([
                    'Computer Science',
                    'Electronics and telecommunication',
                    'Electrotechnics',
                ]),
                'semester' => $this->faker->numberBetween(1, 7),
                'year_of_study' => $year . ' ' . ++$year,
                'mode_of_study' => $this->faker->randomElement(['stationary', 'non-stationary']),
            ]);
        }

        }
}