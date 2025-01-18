<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Supervisor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeeklyReportStatus>
 */
class WeeklyReportStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_number' => Student::factory(),
            'supervisor_id' => Supervisor::factory(),
            'week' => fake()->numberBetween(0, 6),
            'status' => fake()->randomElement(['rejected', 'unsubmitted', 'submitted', 'validated']),
        ];
    }
}
