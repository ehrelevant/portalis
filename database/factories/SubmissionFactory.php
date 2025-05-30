<?php

namespace Database\Factories;

use App\Models\SubmissionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'submission_status_id' => SubmissionStatus::factory(),
            'submission_number' => fake()->numberBetween(1, 100),
            'filepath' => fake()->file('/', '/storage/app/private/student/documents'),
        ];
    }
}
