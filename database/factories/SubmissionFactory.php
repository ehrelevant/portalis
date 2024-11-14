<?php

namespace Database\Factories;

use App\Models\Requirement;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SubmissionFactory extends Factory
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
            'submission_number' => fake()->numberBetween(1, 100),
            'filepath' => fake()->file('/', '/storage/app/private/student/documents'),
        ];
    }
}
