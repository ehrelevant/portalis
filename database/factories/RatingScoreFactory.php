<?php

namespace Database\Factories;

use App\Models\FormStatus;
use App\Models\RatingQuestion;
use App\Models\User;
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
            'form_status_id' => FormStatus::factory(),
            'rating_question_id' => RatingQuestion::factory(),
            'user_id' => User::factory(),
            'score' => null
        ];
    }
}
