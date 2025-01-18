<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Supervisor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InternEvaluationStatus>
 */
class InternEvaluationStatusFactory extends Factory
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
            'supervisor_id' => Supervisor::factory(),
            'status' => fake()->randomElement(['rejected', 'unsubmitted', 'submitted', 'validated']),
        ];
    }
}
