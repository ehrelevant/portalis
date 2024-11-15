<?php

namespace Database\Seeders;

use App\Models\OpenEndedQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpenEndedQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OpenEndedQuestion::factory()->create([
            'question' => 'Any comments or concerns?',
        ]);
    }
}
