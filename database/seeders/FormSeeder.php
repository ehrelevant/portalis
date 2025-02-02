<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\FormOpenQuestion;
use App\Models\FormRatingQuestion;
use App\Models\FormStatus;
use App\Models\OpenQuestion;
use App\Models\RatingCategory;
use App\Models\RatingQuestion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Form setup
        Form::factory()->create([
            'form_name' => 'Midsem Report'
        ]);
        Form::factory()->create([
            'form_name' => 'Final Report'
        ]);
        Form::factory()->create([
            'form_name' => 'Company Evaluation'
        ]);
        Form::factory()->create([
            'form_name' => 'Self Assessment'
        ]);
        Form::factory()->create([
            'form_name' => 'Intern Assessment'
        ]);

        // Open-ended Question Setup
        OpenQuestion::factory()->create([
            'question' => 'Any comments or concerns?',
        ]);
        OpenQuestion::factory()->create([
            'question' => 'What are the intern’s main strengths? In terms of technical skills, work ethic, etc.?',
        ]);
        OpenQuestion::factory()->create([
            'question' => 'Are there areas (communication, technical, work ethic, etc.) where the intern seems to be lacking?',
        ]);
        OpenQuestion::factory()->create([
            'question' => 'Would you consider the intern to be “industry-ready?“ Would you consider hiring the intern after he/she graduates?',
        ]);

        // Rating Category Setup
        RatingCategory::factory()->create([
            'category_name' => 'Non-Technical Criteria'
        ]);
        RatingCategory::factory()->create([
            'category_name' => 'Technical Criteria'
        ]);

        // Rating Question Setup
        RatingQuestion::factory()->create([
            'rating_category_id' => 1,
            'criterion' => 'Work Ethic',
            'max_score' => 10,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 1,
            'criterion' => 'Attitude and Personality',
            'max_score' => 10,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 1,
            'criterion' => 'Attendance and Punctuality',
            'max_score' => 10,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 1,
            'criterion' => 'Respect for Authority',
            'max_score' => 10,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 2,
            'criterion' => 'Technical Output',
            'max_score' => 60,
        ]);

        // Connecting Forms to Open-ended Questions
        FormOpenQuestion::factory()->create([
            'form_id' => 1,
            'open_question_id' => 1
        ]);
        for ($i = 2; $i <= 4; $i++) {
            FormOpenQuestion::factory()->create([
                'form_id' => 2,
                'open_question_id' => $i
            ]);
        }

        // Connecting Forms to Rating Questions
        for ($i = 1; $i <= 5; $i++) {
            FormRatingQuestion::factory()->create([
                'form_id' => 1,
                'rating_question_id' => $i,
            ]);
            FormRatingQuestion::factory()->create([
                'form_id' => 2,
                'rating_question_id' => $i,
            ]);
        }

        // Form Status Setup
        foreach (User::all() as $user) {
            if ($user->role === 'supervisor') {
                FormStatus::factory()->create([
                    'form_id' => 1,
                    'user_id' => $user->id,
                    'status' => 'unsubmitted',
                ]);
                FormStatus::factory()->create([
                    'form_id' => 2,
                    'user_id' => $user->id,
                    'status' => 'unsubmitted',
                ]);
            }
        }
    }
}
