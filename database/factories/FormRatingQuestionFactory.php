<?php

namespace Database\Factories;

use App\Models\Form;
use App\Models\RatingQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormRatingQuestion>
 */
class FormRatingQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'rating_question_id' => RatingQuestion::factory(),
        ];
    }
}
