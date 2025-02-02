<?php

namespace Database\Factories;

use App\Models\FormAnswer;
use App\Models\OpenQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OpenAnswer>
 */
class OpenAnswerFactory extends Factory
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
            'open_question_id' => OpenQuestion::factory(),
            'answer' => null
        ];
    }
}
