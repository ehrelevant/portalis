<?php

namespace Database\Factories;

use App\Models\RatingCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RatingQuestion>
 */
class RatingQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rating_category_id' => RatingCategory::factory(),
            'criterion' => fake()->text(20),
            'max_score' => null,
            'max_score' => null,
        ];
    }
}
