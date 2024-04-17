<?php
declare(strict_types=1);

require_once "vendor/autoload.php";

use Faker\Factory;
use Faker\Generator;

class RandomCoursesGenerator
{
    private array $randomCourses;
    private Generator $faker;

    public function __construct(int $quantity, bool $saveToFile = true)
    {
        $this->randomCourses = [];
        $this->faker = Factory::create('pl_PL');
        $this->generateRandomCourses($quantity);
        $this->saveRandomCoursesToFile();
    }

    public function getRandomCourses(): array
    {
        return $this->randomCourses;
    }

    public function saveRandomCoursesToFile(string $filename='courses.json'): bool
    {
        $fileDir = __DIR__ . '../../../' . $filename;

        if (file_exists($fileDir)) {
            if (!unlink($fileDir)) {
                echo "Cannot delete courses file!";
                return false;
            }
        } else {
            touch($fileDir);
        }

        return file_put_contents($fileDir, json_encode($this->randomCourses)) !== false;
    }

    public function generateRandomCourses(int $quantity) : void
    {
        for ($i = 0; $i < $quantity; $i++) {
            $this->randomCourses[] = [
                'name' => $this->faker->sentence(5),
                'description' => $this->faker->text,
                'available_from' => $this->faker->dateTimeBetween('-3 weeks', '+3 weeks')->format('Y-m-d H:i:s'),
                'available_to' => $this->faker->dateTimeBetween('+4 weeks', '12 weeks')->format('Y-m-d H:i:s')
            ];
        }
    }
}