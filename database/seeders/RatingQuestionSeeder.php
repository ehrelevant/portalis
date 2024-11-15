<?php

namespace Database\Seeders;

use App\Models\RatingQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RatingQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RatingQuestion::factory()->create([
            'criterion' => 'Work Ethic',
            'maximum_score' => 10
        ]);
        RatingQuestion::factory()->create([
            'criterion' => 'Attitude and Personality',
            'maximum_score' => 10
        ]);
        RatingQuestion::factory()->create([
            'criterion' => 'Attendance and Punctuality',
            'maximum_score' => 10
        ]);
        RatingQuestion::factory()->create([
            'criterion' => 'Respect for Authority',
            'maximum_score' => 10
        ]);
        RatingQuestion::factory()->create([
            'criterion' => 'Technical Output',
            'maximum_score' => 60
        ]);
    }
}
