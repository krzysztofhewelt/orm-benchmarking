<?php
declare(strict_types=1);

use Faker\Factory;

function generateRandomCourses(int $quantity) : array
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

function generateRandomUsers(int $quantity) : array
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