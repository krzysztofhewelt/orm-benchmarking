<?php
declare(strict_types=1);

require "vendor/autoload.php";

use Faker\Factory;
use Faker\Generator;

class RandomUsersGenerator
{
    private array $randomUsers;
    private Generator $faker;

    public function __construct(int $quantity, bool $saveToFile = true)
    {
        $this->randomUsers = [];
        $this->faker = Factory::create('pl_PL');
        $this->generateRandomUsers($quantity);

        if($saveToFile)
            $this->saveRandomUsersToFile();
    }

    public function getRandomUsers(): array
    {
        return $this->randomUsers;
    }

    public function saveRandomUsersToFile(string $filename = 'users.json'): bool
    {
        $fileDir = __DIR__ . '../../../' . $filename;

        if (file_exists($fileDir)) {
            if (!unlink($fileDir)) {
                echo "Cannot delete users file!";
                return false;
            }
        } else {
            touch($fileDir);
        }

        return file_put_contents($fileDir, json_encode($this->randomUsers)) !== false;
    }

    public function generateRandomUsers(int $quantity): void
    {
        for ($i = 0; $i < $quantity; $i++) {
            $userData = $this->generateUserInformation($i + 1);
            if ($userData['account_role'] == 'student')
                $userData['student'] = $this->generateStudentInformation();
            elseif ($userData['account_role'] == 'teacher')
                $userData['teacher'] = $this->generateTeacherInformation();

            $this->randomUsers[] = $userData;
        }
    }

    private function generateUserInformation(int $userNumber): array
    {
        return ['name' => $this->faker->firstName,
            'surname' => $this->faker->lastName,
            'email' => 'email_new_' . $userNumber . '@email.com',
            'account_role' => $this->faker->randomElement(['student', 'teacher']),
            'password' => '$2a$12$zBnz52gjTjWlkWQ0vIAibuCAwFQtj9v3D2xsGMmm2lxj4NMlIJfn.',
            'active' => $this->faker->numberBetween(0, 1)
        ];
    }

    private function generateStudentInformation(): array
    {
        $year = $this->faker->numberBetween(2022, 2025);

        return ['field_of_study' => $this->faker->randomElement([
            'Computer Science',
            'Electronics and telecommunication',
            'Electrotechnics',
        ]),
            'semester' => $this->faker->numberBetween(1, 7),
            'year_of_study' => $year . ' ' . ++$year,
            'mode_of_study' => $this->faker->randomElement(['stationary', 'non-stationary']),
        ];
    }

    private function generateTeacherInformation(): array
    {
        return [
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
        ];
    }
}
