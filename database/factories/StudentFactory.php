<?php

namespace Database\Factories;

use App\Models\Faculty;
use App\Models\Supervisor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_number' => strval(fake()->randomNumber(9)),
            'supervisor_id' => Supervisor::factory(),
            'faculty_id' => Faculty::factory(),
            'grade' => fake()->randomFloat(),
            'wordpress_name' => fake()->name(),
            'wordpress_email' => fake()->unique()->safeEmail(),
        ];
    }
}
