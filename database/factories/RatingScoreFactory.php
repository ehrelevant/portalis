<?php

namespace Database\Factories;

use App\Models\FormAnswer;
use App\Models\RatingQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RatingScore>
 */
class RatingScoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_answer_id' => FormAnswer::factory(),
            'rating_question_id' => RatingQuestion::factory(),
            'score' => null
        ];
    }
}
