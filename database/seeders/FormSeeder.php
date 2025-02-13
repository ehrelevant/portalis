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
            'short_name' => 'company-evaluation',
            'phase' => 'during',
            'deadline' => null,
        ]);
        Form::factory()->create([
            'form_name' => 'Intern Self Evaluation',
            'short_name' => 'self-evaluation',
            'phase' => 'post',
            'deadline' => null,
        ]);
        Form::factory()->create([
            'form_name' => 'Company Intern Evaluation',
            'short_name' => 'intern-evaluation',
            'phase' => 'post',
            'deadline' => null,
        ]);

        // Open-ended Question Setup
        OpenQuestion::factory()->create([
            'question' => 'If you have any comments or observations about this intern, please write them below:',
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
        OpenQuestion::factory()->create([
            'question' => 'Any comments or concerns?',
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
        RatingCategory::factory()->create([
            'category_name' => 'Total Hours',
        ]);

        // Rating Question Setup
        RatingQuestion::factory()->create([
            'rating_category_id' => 1,
            'criterion' => 'Work Ethic (10)',
            'min_score' => 0,
            'max_score' => 10,
            'tooltip' => "[0-3] Performs very little of the assigned work; Some of the assigned work never gets completed and reflects very little effort; Often needs reminding; Failed to demonstrate integrity in speech or action at all times.
[4-6] Performs most of the assigned work though sometimes needs reminding; Work reflects some effort, and is sometimes accomplished late.
[7-10] Performs all of the assigned work without any need of reminders; Work reflects best effort, and is ready on time or sometimes ahead of time; Demonstrates integrity in speech and action at all times.

",
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 1,
            'criterion' => 'Attitude and Personality (10)',
            'min_score' => 0,
            'max_score' => 10,
            'tooltip' => "[0-3] Displays negative attitude towards the tasks; Does not get along well with most people; Often publicly critical of others; Does not dress appropriately most of the time.
[4-6] Displays a positive attitude towards the assigned tasks most of the time but encounters difficulty in getting along with some people; Sometimes publicly critical of others; Wears inappropriate attire in one or two absences.
[7-10] Displays a positive attitude towards the assigned tasks; Never publicly critical of others; Wears appropriate attire
",
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 1,
            'criterion' => 'Attendance and Punctuality (10)',
            'min_score' => 0,
            'max_score' => 10,
            'tooltip' => "[0-3] The intern is often tardy or has incurred more than three unexcused absences.
[4-6] The intern has incurred no more than three tardiness and/or unexcused absences.
[7-10] Generally, the intern reports in time with no more than one unexcused absence and/or tardiness.
",
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 1,
            'criterion' => 'Respect for Authority (10)',
            'min_score' => 0,
            'max_score' => 10,
            'tooltip' => "[0-3] Often rude or impolite; Rarely listens to and supports the efforts of others; Often disregards rules and regulations.
[4-6] Courteous and respectful to superiors and colleagues most of the time; Often listens to and supports the efforts of others; Obeys rules and regulations most of the time.
[7-10] Courteous and respectful to superiors and colleagues at all times; Listens and supports the efforts of others; Obeys rules and regulations at all times.
",
        ]);
        RatingQuestion::factory()->create([
            'rating_category_id' => 2,
            'criterion' => 'Technical Output (60)',
            'min_score' => 0,
            'max_score' => 60,
            'tooltip' => "[0-10] No technical output, or extremely poor technical output.
[11-20] Some technical output, but of low quality.
[21-60] Good technical output that meets specifications.
",
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
        RatingQuestion::factory()->create([
            'rating_category_id' => 6,
            'criterion' => 'Total number of hours worked over internship period',
            'min_score' => 0,
            'max_score' => null,
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
        FormOpenQuestion::factory()->create([
            'form_id' => 3,
            'open_question_id' => 5,
        ]);
        FormOpenQuestion::factory()->create([
            'form_id' => 4,
            'open_question_id' => 5,
        ]);
        FormOpenQuestion::factory()->create([
            'form_id' => 5,
            'open_question_id' => 5,
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
        FormRatingQuestion::factory()->create([
            'form_id' => 4,
            'rating_question_id' => 14,
        ]);

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
