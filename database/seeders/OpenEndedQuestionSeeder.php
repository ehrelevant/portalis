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
        OpenEndedQuestion::factory()->create([
            'question' => 'What are the intern’s main strengths? In terms of technical skills, work ethic, etc.?',
        ]);
        OpenEndedQuestion::factory()->create([
            'question' => 'Are there areas (communication, technical, work ethic, etc.) where the intern seems to be lacking?',
        ]);
        OpenEndedQuestion::factory()->create([
            'question' => 'Would you consider the intern to be “industry-ready?“ Would you consider hiring the intern after he/she graduates?',
        ]);
    }
}
