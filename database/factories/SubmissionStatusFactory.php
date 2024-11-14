<?php

namespace Database\Factories;

use App\Models\Requirement;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubmissionStatus>
 */
class SubmissionStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_number' => Student::factory(),
            'requirement_id' => Requirement::factory(),
            'status' => fake()->randomElement(['pending', 'submitted', 'validated']),
        ];
    }
}
