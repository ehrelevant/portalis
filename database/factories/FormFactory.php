<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form>
 */
class FormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_name' => Str::random(30),
            'short_name' => Str::random(10),
            'phase' => fake()->randomElement(['pre', 'during', 'post']),
            'deadline' => fake()->dateTime(),
        ];
    }
}
