<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyEvaluationStatus>
 */
class CompanyEvaluationStatusFactory extends Factory
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
            'company_id' => Company::factory(),
            'status' => fake()->randomElement(['pending', 'submitted', 'validated']),
        ];
    }
}
