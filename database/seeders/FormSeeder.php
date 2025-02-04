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
            'form_name' => 'Midsem Report',
            'short_name' => 'midsem',
            'phase' => 'during',
            'deadline' => null,
        ]);
        Form::factory()->create([
            'form_name' => 'Final Report',
            'short_name' => 'final',
            'phase' => 'during',
            'deadline' => null,
        ]);
        Form::factory()->create([
            'form_name' => 'Company Evaluation',
            'short_name' => 'company',
            'phase' => 'during',
            'deadline' => null,
        ]);
        Form::factory()->create([
            'form_name' => 'Self Assessment',
            'short_name' => 'self-assessment',
            'phase' => 'post',
            'deadline' => null,
        ]);
        Form::factory()->create([
            'form_name' => 'Intern Assessment',
            'short_name' => 'intern-assessment',
            'phase' => 'post',
            'deadline' => null,
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
            'category_name' => 'Non-Technical Criteria',
        ]);
        RatingCategory::factory()->create([
            'category_name' => 'Technical Criteria',
        ]);
        RatingCategory::factory()->create([
            'category_name' => 'Company Ratings',
        ]);
        RatingCategory::factory()->create([
            'category_name' => 'Supervisor Ratings',
        ]);
        RatingCategory::factory()->create([
            'category_name' => 'Overall Ratings',
        ]);

        // Rating Question Setup
        RatingQuestion::factory()->create([
            'rating_category_id' => 1,
            'criterion' => 'Work Ethic (10)',
            'min_score' => 0,
            'max_score' => 10,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 1,
            'criterion' => 'Attitude and Personality (10)',
            'min_score' => 0,
            'max_score' => 10,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 1,
            'criterion' => 'Attendance and Punctuality (10)',
            'min_score' => 0,
            'max_score' => 10,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 1,
            'criterion' => 'Respect for Authority (10)',
            'min_score' => 0,
            'max_score' => 10,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 2,
            'criterion' => 'Technical Output (60)',
            'min_score' => 0,
            'max_score' => 60,
        ]);

        RatingQuestion::factory()->create([
            'rating_category_id' => 3,
            'criterion' => 'How would you rate your learning experience with your company? (1-6)',
            'min_score' => 1,
            'max_score' => 6,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 3,
            'criterion' => 'How would you rate the usefulness of your learnings from your company? (1-6)',
            'min_score' => 1,
            'max_score' => 6,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 3,
            'criterion' => 'How would you rate your overall experience at your company? (1-6)',
            'min_score' => 1,
            'max_score' => 6,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 4,
            'criterion' => 'How would you rate the helpfulness of your company supervisor? (1-6)',
            'min_score' => 1,
            'max_score' => 6,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 4,
            'criterion' => 'How would you rate the involvement of your company supervisor in your learning experience? (1-6)',
            'min_score' => 1,
            'max_score' => 6,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 4,
            'criterion' => 'How would you rate your experience with your company supervisor? (1-6)',
            'min_score' => 1,
            'max_score' => 6,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 5,
            'criterion' => 'How much would you recommend working at your company to other students? (1-6)',
            'min_score' => 1,
            'max_score' => 6,
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 5,
            'criterion' => 'How much would you recommend working under your company supervisor? (1-6)',
            'min_score' => 1,
            'max_score' => 6,
        ]);

        // Connecting Forms to Open-ended Questions
        FormOpenQuestion::factory()->create([
            'form_id' => 1,
            'open_question_id' => 1,
        ]);
        for ($i = 2; $i <= 4; $i++) {
            FormOpenQuestion::factory()->create([
                'form_id' => 2,
                'open_question_id' => $i,
            ]);
        }
        FormRatingQuestion::factory()->create([
            'form_id' => 3,
            'rating_question_id' => 1,
        ]);

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
        for ($i = 6; $i <= 13; $i++) {
            FormRatingQuestion::factory()->create([
                'form_id' => 3,
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
                FormStatus::factory()->create([
                    'form_id' => 5,
                    'user_id' => $user->id,
                    'status' => 'unsubmitted',
                ]);
            }

            if ($user->role === 'student') {
                FormStatus::factory()->create([
                    'form_id' => 3,
                    'user_id' => $user->id,
                    'status' => 'unsubmitted',
                ]);
                FormStatus::factory()->create([
                    'form_id' => 4,
                    'user_id' => $user->id,
                    'status' => 'unsubmitted',
                ]);
            }
        }
    }
}
