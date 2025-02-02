<?php

namespace Database\Factories;

use App\Models\FormStatus;
use App\Models\OpenQuestion;
use App\Models\User;
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
            'form_status_id' => FormStatus::factory(),
            'open_question_id' => OpenQuestion::factory(),
            'user_id' => User::factory(),
            'answer' => null
        ];
    }
}
