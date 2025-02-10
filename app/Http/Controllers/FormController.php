<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormAnswer;
use App\Models\FormStatus;
use App\Models\InternEvaluation;
use App\Models\InternEvaluationStatus;
use App\Models\OpenAnswer;
use App\Models\RatingScore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class FormController extends Controller
{
    private function getOpenQuestions(int $form_id)
    {
        return DB::table('form_open_questions')
            ->where('form_id', $form_id)
            ->join(
                'open_questions',
                'open_questions.id',
                '=',
                'form_open_questions.open_question_id'
            )
            ->pluck('open_questions.question', 'open_questions.id');
    }

    private function getRatingCategories(int $form_id)
    {
        return DB::table('forms')
            ->where('forms.id', $form_id)
            ->join('form_rating_questions', 'form_rating_questions.form_id', '=', 'forms.id')
            ->join('rating_questions', 'rating_questions.id', '=', 'form_rating_questions.rating_question_id')
            ->join('rating_categories', 'rating_categories.id', '=', 'rating_questions.rating_category_id')
            ->select('rating_categories.id', 'rating_categories.category_name')
            ->groupBy('rating_categories.id', 'rating_categories.category_name')
            ->get();
    }

    private function getCategorizedRatingQuestions(int $form_id, $rating_categories)
    {
        $categorized_rating_questions = [];

        foreach ($rating_categories as $category) {
            $rating_questions = DB::table('form_rating_questions')
                ->where('form_id', $form_id)
                ->join(
                    'rating_questions',
                    'rating_questions.id',
                    '=',
                    'form_rating_questions.rating_question_id'
                )
                ->where('rating_questions.rating_category_id', $category->id)
                ->select(
                    'rating_questions.id AS rating_question_id',
                    'rating_questions.min_score',
                    'rating_questions.max_score',
                    'rating_questions.criterion'
                )
                ->get();

            foreach ($rating_questions as $rating_question) {
                $categorized_rating_questions[$category->id][$rating_question->rating_question_id] =
                    [
                        'criterion' => $rating_question->criterion,
                        'min_score' =>  $rating_question->min_score,
                        'max_score' =>  $rating_question->max_score,
                    ];
            }
        }
        return $categorized_rating_questions;
    }

    private function getFormInfo(int $form_id)
    {
        return DB::table('forms')
            ->where('id', $form_id)
            ->select('form_name', 'short_name')
            ->firstOrFail();
    }

    private function queryFormStatus(int $user_id, string $form_short_name)
    {
        return DB::table('form_statuses')
            ->where('user_id', $user_id)
            ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
            ->where('short_name', $form_short_name)
            ->select('form_statuses.form_id', 'form_statuses.id AS form_status_id');
    }

    private function queryFormAnswers(int $user_id, string $form_short_name)
    {
        return DB::table('form_statuses')
            ->where('form_statuses.user_id', $user_id)
            ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
            ->where('short_name', $form_short_name)
            ->join('form_answers', 'form_answers.form_status_id', '=', 'form_statuses.id')
            ->select(
                'form_statuses.form_id',
                'form_statuses.id AS form_status_id',
                'form_answers.id AS form_answer_id',
                'form_answers.evaluated_user_id'
            );
    }

    private function createForm($form_status, $user_ids)
    {
        $form_rating_question_ids = DB::table('form_rating_questions')
            ->where('form_id', $form_status->form_id)
            ->join('rating_questions', 'rating_questions.id', '=', 'form_rating_questions.rating_question_id')
            ->pluck('form_rating_questions.rating_question_id');

        $form_open_question_ids = DB::table('form_open_questions')
            ->where('form_id', $form_status->form_id)
            ->join('open_questions', 'open_questions.id', '=', 'form_open_questions.open_question_id')
            ->pluck('form_open_questions.open_question_id');

        // Generate form items if it doesn't already exist
        foreach ($user_ids as $user_id) {
            $new_form_answer = new FormAnswer();
            $new_form_answer->form_status_id = $form_status->form_status_id;
            $new_form_answer->evaluated_user_id = $user_id;
            $new_form_answer->save();

            foreach ($form_rating_question_ids as $form_rating_question_id) {
                $rating_score = new RatingScore();
                $rating_score->form_answer_id = $new_form_answer->id;
                $rating_score->rating_question_id = $form_rating_question_id;
                $rating_score->save();
            }

            foreach ($form_open_question_ids as $form_open_question_id) {
                $open_answer = new OpenAnswer();
                $open_answer->form_answer_id = $new_form_answer->id;
                $open_answer->open_question_id = $form_open_question_id;
                $open_answer->save();
            }
        }
    }

    // Report Form Functions
    public function getSavedReportValues($form_answers, $rating_categories)
    {
        $students = [];

        foreach ($form_answers as $form_answer) {
            $form_answer_id = $form_answer->form_answer_id;
            $student_user_id = $form_answer->evaluated_user_id;

            $student_info = DB::table('users')
                ->where('id', $student_user_id)
                ->select('first_name', 'last_name', 'role_id AS student_number')
                ->firstOrFail();

            $categorized_ratings = [];
            foreach ($rating_categories as $category) {
                $ratings = DB::table('rating_scores')
                    ->where('form_answer_id', $form_answer_id)
                    ->join('rating_questions', 'rating_questions.id', '=', 'rating_scores.rating_question_id')
                    ->where('rating_questions.rating_category_id', $category->id)
                    ->pluck('rating_scores.score', 'rating_scores.rating_question_id')
                    ->toArray();

                $categorized_ratings[$category->id] = $ratings;
            }

            $opens = DB::table('open_answers')
                ->where('form_answer_id', $form_answer_id)
                ->pluck('answer', 'open_question_id')
                ->toArray();

            $students[$student_info->student_number] = [
                'student_number' => $student_info->student_number,
                'last_name' => $student_info->last_name,
                'first_name' => $student_info->first_name,
                'categorized_ratings' => $categorized_ratings,
                'opens' => $opens,
            ];
        }

        return $students;
    }

    public function answerReportForm(string $short_name)
    {
        $supervisor_user = Auth::user();

        $form_status = $this->queryFormStatus($supervisor_user->id, $short_name)->firstOrFail();
        $form_answers = $this->queryFormAnswers($supervisor_user->id, $short_name)->get();

        if ($form_answers->isEmpty()) {
            $supervised_student_user_ids = DB::table('users')
                ->where('role', 'student')
                ->join('students', 'students.student_number', '=', 'users.role_id')
                ->where('supervisor_id', $supervisor_user->role_id)
                ->pluck('users.id');

            $this->createForm($form_status, $supervised_student_user_ids);
            $form_answers = $this->queryFormAnswers($supervisor_user->id, $short_name)->get();;
        }

        $rating_categories = $this->getRatingCategories($form_status->form_id);
        $students = $this->getSavedReportValues($form_answers, $rating_categories);
        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        return Inertia::render('form/report/Answer', [
            'students' => $students,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
        ]);
    }

    public function viewReportForm(string $short_name, int $supervisor_id)
    {
        $user_id = DB::table('users')
            ->where('role', 'supervisor')
            ->where('role_id', $supervisor_id)
            ->firstOrFail()
            ->id;

        $form_status = $this->queryFormStatus($user_id, $short_name)->firstOrFail();
        $form_answers = $this->queryFormAnswers($user_id, $short_name)->get();

        $rating_categories = $this->getRatingCategories($form_status->form_id);
        $students = $this->getSavedReportValues($form_answers, $rating_categories);
        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        $status = DB::table('form_statuses')
            ->where('user_id', $user_id)
            ->where('form_id', $form_status->form_id)
            ->firstOrFail()
            ->status;

        return Inertia::render('form/report/View', [
            'evaluator_user_id' => $user_id,
            'students' => $students,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
            'status' => $status,
        ]);
    }

    public function updateReportForm($form_values, string $short_name)
    {
        $supervisor_user = Auth::user();

        $form_id = Form::where('short_name', $short_name)->firstOrFail()->id;

        foreach ($form_values['answers'] as $evaluation) {
            $form_status_id = FormStatus::where('user_id', $supervisor_user->id)
                ->where('form_id', $form_id)
                ->firstOrFail()
                ->id;

            $form_answer = FormAnswer::where('form_status_id', $form_status_id)
                ->join('users', 'users.id', '=', 'form_answers.evaluated_user_id')
                ->where('role', 'student')
                ->where('role_id', $evaluation['student_number'])
                ->select('form_answers.id')
                ->firstOrFail();

            foreach ($evaluation['categorized_ratings'] as $ratings) {
                foreach ($ratings as $rating_question_id => $score) {
                    $form_rating_score = RatingScore::where('form_answer_id', $form_answer->id)
                        ->where('rating_question_id', $rating_question_id)
                        ->firstOrFail();
                    $form_rating_score->score = $score;
                    $form_rating_score->save();
                }
            }

            foreach ($evaluation['opens'] as $open_question_id => $answer) {
                $form_open_answer = OpenAnswer::where('form_answer_id', $form_answer->id)
                    ->where('open_question_id', $open_question_id)
                    ->firstOrFail();
                $form_open_answer->answer = $answer;
                $form_open_answer->save();
            }
        }
    }

    public function draftReportForm(Request $request, string $short_name)
    {
        $form_values = $request->validate([
            'answers' => ['array'],
            'answers.*.student_number' => ['integer', 'numeric'],
            'answers.*.categorized_ratings' => ['array'],
            'answers.*.categorized_ratings.*' => ['array'],
            'answers.*.categorized_ratings.*.*' => ['nullable'],
            'answers.*.opens' => ['array'],
            'answers.*.opens.*' => ['nullable'],
        ]);

        $this->updateReportForm($form_values, $short_name);

        return redirect('/dashboard');
    }

    public function submitReportForm(Request $request, string $short_name)
    {
        $form_values = $request->validate([
            'answers' => ['array'],
            'answers.*.student_number' => ['integer', 'numeric'],
            'answers.*.categorized_ratings' => ['array'],
            'answers.*.categorized_ratings.*' => ['array'],
            'answers.*.categorized_ratings.*.*' => ['required', 'integer', 'numeric'],
            'answers.*.opens' => ['array'],
            'answers.*.opens.*' => ['nullable', 'string'],
        ]);

        $this->updateReportForm($form_values, $short_name);

        $user = Auth::user();

        // Update status for all reports under the supervisor
        FormStatus::where('user_id', $user->id)
            ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
            ->where('forms.short_name', $short_name)
            ->update(['status' => 'submitted']);

        return redirect('/dashboard');
    }

    // Company Evaluation Forms
    private function getSavedCompanyEvaluationValues($form_answer, $rating_categories)
    {
        $form_answer_id = $form_answer->form_answer_id;

        $categorized_ratings = [];
        foreach ($rating_categories as $category) {
            $ratings = DB::table('rating_scores')
                ->where('form_answer_id', $form_answer_id)
                ->join('rating_questions', 'rating_questions.id', '=', 'rating_scores.rating_question_id')
                ->where('rating_questions.rating_category_id', $category->id)
                ->pluck('rating_scores.score', 'rating_scores.rating_question_id')
                ->toArray();

            $categorized_ratings[$category->id] = $ratings;
        }

        $opens = DB::table('open_answers')
            ->where('form_answer_id', $form_answer_id)
            ->pluck('answer', 'open_question_id')
            ->toArray();

        return [
            'categorized_ratings' => $categorized_ratings,
            'opens' => $opens,
        ];
    }

    public function answerCompanyEvaluationForm()
    {
        $short_name = 'company-evaluation';
        $student_user = Auth::user();

        $form_status = $this->queryFormStatus($student_user->id, $short_name)->firstOrFail();
        $form_answer = $this->queryFormAnswers($student_user->id, $short_name)->first();

        if (!$form_answer) {
            $this->createForm($form_status, [null]);
            $form_answer = $this->queryFormAnswers($student_user->id, $short_name)->first();
        }

        $rating_categories = $this->getRatingCategories($form_status->form_id);
        $values = $this->getSavedCompanyEvaluationValues($form_answer, $rating_categories);
        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        return Inertia::render('form/company-evaluation/Answer', [
            'values' => $values,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
        ]);
    }

    public function viewCompanyEvaluationForm(int $student_number)
    {
        $short_name = 'company-evaluation';

        $user_id = DB::table('users')
            ->where('role', 'student')
            ->where('role_id', $student_number)
            ->firstOrFail()
            ->id;

        $form_status = $this->queryFormStatus($user_id, $short_name)->firstOrFail();
        $form_answer = $this->queryFormAnswers($user_id, $short_name)->firstOrFail();

        $rating_categories = $this->getRatingCategories($form_status->form_id);
        $values = $this->getSavedCompanyEvaluationValues($form_answer, $rating_categories);
        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        $status = DB::table('form_statuses')
            ->where('user_id', $user_id)
            ->where('form_id', $form_status->form_id)
            ->firstOrFail()
            ->status;

        return Inertia::render('form/company-evaluation/View', [
            'evaluator_user_id' => $user_id,
            'values' => $values,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
            'status' => $status,
        ]);
    }

    public function updateCompanyEvaluationForm($evaluation, string $short_name)
    {
        $student_user = Auth::user();

        $form_id = Form::where('short_name', $short_name)->firstOrFail()->id;

        $form_status_id = FormStatus::where('user_id', $student_user->id)
            ->where('form_id', $form_id)
            ->firstOrFail()
            ->id;

        $form_answer = FormAnswer::where('form_status_id', $form_status_id)
            ->select('form_answers.id')
            ->firstOrFail();

        foreach ($evaluation['categorized_ratings'] as $ratings) {
            foreach ($ratings as $rating_question_id => $score) {
                $form_rating_score = RatingScore::where('form_answer_id', $form_answer->id)
                    ->where('rating_question_id', $rating_question_id)
                    ->firstOrFail();
                $form_rating_score->score = $score;
                $form_rating_score->save();
            }
        }

        foreach ($evaluation['opens'] as $open_question_id => $answer) {
            $form_open_answer = OpenAnswer::where('form_answer_id', $form_answer->id)
                ->where('open_question_id', $open_question_id)
                ->firstOrFail();
            $form_open_answer->answer = $answer;
            $form_open_answer->save();
        }
    }

    public function draftCompanyEvaluationForm(Request $request)
    {
        $short_name = 'company-evaluation';

        $form_values = $request->validate([
            'categorized_ratings' => ['array'],
            'categorized_ratings.*' => ['array'],
            'categorized_ratings.*.*' => ['nullable'],
            'opens' => ['array'],
            'opens.*' => ['nullable'],
        ]);

        $this->updateCompanyEvaluationForm($form_values, $short_name);

        return redirect('/dashboard');
    }

    public function submitCompanyEvaluationForm(Request $request)
    {
        $short_name = 'company-evaluation';

        $form_values = $request->validate([
            'categorized_ratings' => ['array'],
            'categorized_ratings.*' => ['array'],
            'categorized_ratings.*.*' => ['required', 'integer', 'numeric'],
            'opens' => ['array'],
            'opens.*' => ['nullable', 'string'],
        ]);

        $this->updateCompanyEvaluationForm($form_values, $short_name);

        $user = Auth::user();

        // Update status for all reports under the supervisor
        FormStatus::where('user_id', $user->id)
            ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
            ->where('forms.short_name', $short_name)
            ->update(['status' => 'submitted']);

        return redirect('/dashboard');
    }

    // Self Evaluation Forms
    private function getSavedSelfEvaluationValues($form_answer, $rating_categories)
    {
        $form_answer_id = $form_answer->form_answer_id;

        $categorized_ratings = [];
        foreach ($rating_categories as $category) {
            $ratings = DB::table('rating_scores')
                ->where('form_answer_id', $form_answer_id)
                ->join('rating_questions', 'rating_questions.id', '=', 'rating_scores.rating_question_id')
                ->where('rating_questions.rating_category_id', $category->id)
                ->pluck('rating_scores.score', 'rating_scores.rating_question_id')
                ->toArray();

            $categorized_ratings[$category->id] = $ratings;
        }

        $opens = DB::table('open_answers')
            ->where('form_answer_id', $form_answer_id)
            ->pluck('answer', 'open_question_id')
            ->toArray();

        return [
            'categorized_ratings' => $categorized_ratings,
            'opens' => $opens,
        ];
    }

    public function answerSelfEvaluationForm()
    {
        $short_name = 'self-evaluation';
        $student_user = Auth::user();

        $form_status = $this->queryFormStatus($student_user->id, $short_name)->firstOrFail();
        $form_answer = $this->queryFormAnswers($student_user->id, $short_name)->first();

        if (!$form_answer) {
            $this->createForm($form_status, [null]);
            $form_answer = $this->queryFormAnswers($student_user->id, $short_name)->first();
        }

        $rating_categories = $this->getRatingCategories($form_status->form_id);
        $values = $this->getSavedSelfEvaluationValues($form_answer, $rating_categories);
        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        return Inertia::render('form/self-evaluation/Answer', [
            'values' => $values,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
        ]);
    }

    public function viewSelfEvaluationForm(int $student_number)
    {
        $short_name = 'self-evaluation';

        $user_id = DB::table('users')
            ->where('role', 'student')
            ->where('role_id', $student_number)
            ->firstOrFail()
            ->id;

        $form_status = $this->queryFormStatus($user_id, $short_name)->firstOrFail();
        $form_answer = $this->queryFormAnswers($user_id, $short_name)->firstOrFail();

        $rating_categories = $this->getRatingCategories($form_status->form_id);
        $values = $this->getSavedSelfEvaluationValues($form_answer, $rating_categories);
        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        $status = DB::table('form_statuses')
            ->where('user_id', $user_id)
            ->where('form_id', $form_status->form_id)
            ->firstOrFail()
            ->status;

        return Inertia::render('form/self-evaluation/View', [
            'evaluator_user_id' => $user_id,
            'values' => $values,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
            'status' => $status,
        ]);
    }

    public function updateSelfEvaluationForm($evaluation, $short_name)
    {
        $student_user = Auth::user();

        $form_id = Form::where('short_name', $short_name)->firstOrFail()->id;

        $form_status_id = FormStatus::where('user_id', $student_user->id)
            ->where('form_id', $form_id)
            ->firstOrFail()
            ->id;

        $form_answer = FormAnswer::where('form_status_id', $form_status_id)
            ->select('form_answers.id')
            ->firstOrFail();

        foreach ($evaluation['categorized_ratings'] as $ratings) {
            foreach ($ratings as $rating_question_id => $score) {
                $form_rating_score = RatingScore::where('form_answer_id', $form_answer->id)
                    ->where('rating_question_id', $rating_question_id)
                    ->firstOrFail();
                $form_rating_score->score = $score;
                $form_rating_score->save();
            }
        }

        foreach ($evaluation['opens'] as $open_question_id => $answer) {
            $form_open_answer = OpenAnswer::where('form_answer_id', $form_answer->id)
                ->where('open_question_id', $open_question_id)
                ->firstOrFail();
            $form_open_answer->answer = $answer;
            $form_open_answer->save();
        }
    }

    public function draftSelfEvaluationForm(Request $request)
    {
        $short_name = 'self-evaluation';

        $form_values = $request->validate([
            'categorized_ratings' => ['array'],
            'categorized_ratings.*' => ['array'],
            'categorized_ratings.*.*' => ['nullable'],
            'opens' => ['array'],
            'opens.*' => ['nullable'],
        ]);

        $this->updateSelfEvaluationForm($form_values, $short_name);

        return redirect('/dashboard');
    }

    public function submitSelfEvaluationForm(Request $request)
    {
        $short_name = 'self-evaluation';

        $form_values = $request->validate([
            'categorized_ratings' => ['array'],
            'categorized_ratings.*' => ['array'],
            'categorized_ratings.*.*' => ['required', 'integer', 'numeric'],
            'opens' => ['array'],
            'opens.*' => ['nullable', 'string'],
        ]);

        $this->updateSelfEvaluationForm($form_values, $short_name);

        $user = Auth::user();

        // Update status for all reports under the supervisor
        FormStatus::where('user_id', $user->id)
            ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
            ->where('forms.short_name', $short_name)
            ->update(['status' => 'submitted']);

        return redirect('/dashboard');
    }

    // Intern Evaluation Forms
    private function getSavedInternEvaluationValues($form_answers, $rating_categories)
    {
        $students = [];

        foreach ($form_answers as $form_answer) {
            $form_answer_id = $form_answer->form_answer_id;
            $student_user_id = $form_answer->evaluated_user_id;

            $student_info = DB::table('users')
                ->where('id', $student_user_id)
                ->select('id', 'first_name', 'last_name', 'role_id AS student_number')
                ->firstOrFail();

            $categorized_ratings = [];
            foreach ($rating_categories as $category) {
                $ratings = DB::table('rating_scores')
                    ->where('form_answer_id', $form_answer_id)
                    ->join('rating_questions', 'rating_questions.id', '=', 'rating_scores.rating_question_id')
                    ->where('rating_questions.rating_category_id', $category->id)
                    ->pluck('rating_scores.score', 'rating_scores.rating_question_id')
                    ->toArray();

                $categorized_ratings[$category->id] = $ratings;
            }

            $opens = DB::table('open_answers')
                ->where('form_answer_id', $form_answer_id)
                ->pluck('answer', 'open_question_id')
                ->toArray();

            $total_hours = DB::table('form_statuses')
                ->where('user_id', $student_user_id)
                ->join('form_answers', 'form_answers.form_status_id', '=', 'form_statuses.id')
                ->join('rating_scores', 'rating_scores.form_answer_id', '=', 'form_answers.id')
                ->where('rating_question_id', 14) // 14 = Total hours rating question
                ->firstOrFail()
                ->score;

            $self_assessment_status = DB::table('form_statuses')
                ->where('user_id', $student_user_id)
                ->where('form_id', 4) // 4 = Intern self-evaluation form
                ->firstOrFail()
                ->status;

            $students[$student_info->student_number] = [
                'student_user_id' => $student_info->id,
                'student_number' => $student_info->student_number,
                'last_name' => $student_info->last_name,
                'first_name' => $student_info->first_name,
                'categorized_ratings' => $categorized_ratings,
                'opens' => $opens,
                'total_hours' => $total_hours,
                'self_assessment_status' => $self_assessment_status,
            ];
        }

        return $students;
    }

    public function answerInternEvaluationForm()
    {
        $short_name = 'intern-evaluation';
        $supervisor_user = Auth::user();

        $form_status = $this->queryFormStatus($supervisor_user->id, $short_name)->firstOrFail();
        $form_answers = $this->queryFormAnswers($supervisor_user->id, $short_name)->get();

        if ($form_answers->isEmpty()) {
            $supervised_student_user_ids = DB::table('users')
                ->where('role', 'student')
                ->join('students', 'students.student_number', '=', 'users.role_id')
                ->where('supervisor_id', $supervisor_user->role_id)
                ->pluck('users.id');

            $supervised_self_evaluation_statuses = DB::table('users')
                ->where('role', 'student')
                ->join('students', 'students.student_number', '=', 'users.role_id')
                ->where('supervisor_id', $supervisor_user->role_id)
                ->join('form_statuses', 'form_statuses.user_id', '=', 'users.id')
                ->where('form_id', 4)
                ->leftJoin('form_answers', 'form_answers.form_status_id', '=', 'form_statuses.id')
                ->whereNull('form_answers.id')
                ->select('form_statuses.form_id', 'form_statuses.id AS form_status_id')
                ->get();

            foreach ($supervised_self_evaluation_statuses as $supervised_self_evaluation_status) {
                $this->createForm($supervised_self_evaluation_status, [null]);
            }

            $this->createForm($form_status, $supervised_student_user_ids);
            $form_answers = $this->queryFormAnswers($supervisor_user->id, $short_name)->get();;
        }

        $rating_categories = $this->getRatingCategories($form_status->form_id);
        $students = $this->getSavedInternEvaluationValues($form_answers, $rating_categories);
        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        return Inertia::render('form/intern-evaluation/Answer', [
            'students' => $students,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
        ]);
    }

    public function viewInternEvaluationForm(int $supervisor_id)
    {
        $short_name = 'intern-evaluation';
        $user_id = DB::table('users')
            ->where('role', 'supervisor')
            ->where('role_id', $supervisor_id)
            ->firstOrFail()
            ->id;

        $form_status = $this->queryFormStatus($user_id, $short_name)->firstOrFail();
        $form_answers = $this->queryFormAnswers($user_id, $short_name)->get();

        $rating_categories = $this->getRatingCategories($form_status->form_id);
        $students = $this->getSavedInternEvaluationValues($form_answers, $rating_categories);
        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        $status = DB::table('form_statuses')
            ->where('user_id', $user_id)
            ->where('form_id', $form_status->form_id)
            ->firstOrFail()
            ->status;

        return Inertia::render('form/intern-evaluation/View', [
            'evaluator_user_id' => $user_id,
            'students' => $students,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
            'status' => $status,
        ]);
    }

    public function updateInternEvaluationForm($form_values, $short_name)
    {
        $supervisor_user = Auth::user();

        $form_id = Form::where('short_name', $short_name)->firstOrFail()->id;

        foreach ($form_values['answers'] as $evaluation) {
            $form_status_id = FormStatus::where('user_id', $supervisor_user->id)
                ->where('form_id', $form_id)
                ->firstOrFail()
                ->id;

            $form_answer = FormAnswer::where('form_status_id', $form_status_id)
                ->join('users', 'users.id', '=', 'form_answers.evaluated_user_id')
                ->where('role', 'student')
                ->where('role_id', $evaluation['student_number'])
                ->select('form_answers.id')
                ->firstOrFail();

            foreach ($evaluation['categorized_ratings'] as $ratings) {
                foreach ($ratings as $rating_question_id => $score) {
                    $form_rating_score = RatingScore::where('form_answer_id', $form_answer->id)
                        ->where('rating_question_id', $rating_question_id)
                        ->firstOrFail();
                    $form_rating_score->score = $score;
                    $form_rating_score->save();
                }
            }

            foreach ($evaluation['opens'] as $open_question_id => $answer) {
                $form_open_answer = OpenAnswer::where('form_answer_id', $form_answer->id)
                    ->where('open_question_id', $open_question_id)
                    ->firstOrFail();
                $form_open_answer->answer = $answer;
                $form_open_answer->save();
            }
        }
    }

    public function draftInternEvaluationForm(Request $request)
    {
        $short_name = 'intern-evaluation';
        $form_values = $request->validate([
            'answers' => ['array'],
            'answers.*.student_number' => ['integer', 'numeric'],
            'answers.*.categorized_ratings' => ['array'],
            'answers.*.categorized_ratings.*' => ['array'],
            'answers.*.categorized_ratings.*.*' => ['nullable'],
            'answers.*.opens' => ['array'],
            'answers.*.opens.*' => ['nullable'],
        ]);

        $this->updateInternEvaluationForm($form_values, $short_name);

        return redirect('/dashboard');
    }

    public function submitInternEvaluationForm(Request $request)
    {
        $short_name = 'intern-evaluation';
        $form_values = $request->validate([
            'answers' => ['array'],
            'answers.*.student_number' => ['integer', 'numeric'],
            'answers.*.categorized_ratings' => ['array'],
            'answers.*.categorized_ratings.*' => ['array'],
            'answers.*.categorized_ratings.*.*' => ['required', 'integer', 'numeric'],
            'answers.*.opens' => ['array'],
            'answers.*.opens.*' => ['nullable', 'string'],
        ]);

        $this->updateInternEvaluationForm($form_values, $short_name);

        $user = Auth::user();

        // Update status for all reports under the supervisor
        FormStatus::where('user_id', $user->id)
            ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
            ->where('forms.short_name', $short_name)
            ->update(['status' => 'submitted']);

        return redirect('/dashboard');
    }

    public function validateForm(string $short_name, int $user_id): RedirectResponse
    {
        $form_id = Form::where('short_name', $short_name)->firstOrFail()->id;

        $form_status = FormStatus::where('user_id', $user_id)
            ->where('form_id', $form_id)
            ->firstOrFail();

        if ($form_status->status === 'submitted') {
            FormStatus::where('user_id', $user_id)
                ->where('form_id', $form_id)
                ->update(['status' => 'validated']);
        }

        return back();
    }

    public function invalidateForm(string $short_name, int $user_id): RedirectResponse
    {
        $form_id = Form::where('short_name', $short_name)->firstOrFail()->id;

        $form_status = FormStatus::where('user_id', $user_id)
            ->where('form_id', $form_id)
            ->firstOrFail();

        if ($form_status->status === 'validated') {
            FormStatus::where('user_id', $user_id)
                ->where('form_id', $form_id)
                ->update(['status' => 'submitted']);
        }

        return back();
    }

    public function rejectForm(string $short_name, int $user_id): RedirectResponse
    {
        $form_id = Form::where('short_name', $short_name)->firstOrFail()->id;

        $form_status = FormStatus::where('user_id', $user_id)
            ->where('form_id', $form_id)
            ->firstOrFail();

        if ($form_status->status === 'submitted') {
            FormStatus::where('user_id', $user_id)
                ->where('form_id', $form_id)
                ->update(['status' => 'rejected']);
        }

        return back();
    }
}
