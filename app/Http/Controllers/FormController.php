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
            ->join('rating_questions', 'rating_questions.id', '=', 'form_rating_questions.form_id')
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
                    'rating_questions.max_score',
                    'rating_questions.criterion'
                )
                ->get();

            foreach ($rating_questions as $rating_question) {
                $categorized_rating_questions[$category->id][$rating_question->rating_question_id] =
                    [
                        'criterion' => $rating_question->criterion,
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

    public function showWriteableSupervisorForm(string $short_name): Response
    {
        $supervisor_user = Auth::user();
        $supervisor_id = $supervisor_user->role_id;

        $form_statuses_partial = DB::table('form_statuses')
            ->where('form_statuses.user_id', $supervisor_user->id)
            ->join(
                'forms',
                'forms.id',
                '=',
                'form_statuses.form_id'
            )
            ->where('short_name', $short_name)
            ->select('form_statuses.form_id', 'form_statuses.id AS form_status_id');

        $form_status = $form_statuses_partial->firstOrFail();

        $form_answers = $form_statuses_partial
            ->join(
                'form_answers',
                'form_answers.form_status_id',
                '=',
                'form_statuses.id'
            )
            ->select(
                'form_statuses.form_id',
                'form_statuses.id AS form_status_id',
                'form_answers.id AS form_answer_id',
                'form_answers.evaluated_user_id'
            )
            ->get();

        if ($form_answers->isEmpty()) {
            // Hard-coded question ids for the "report" form
            $form_rating_question_ids = DB::table('form_rating_questions')
                ->where('form_id', $form_status->form_id)
                ->join(
                    'rating_questions',
                    'rating_questions.id',
                    '=',
                    'form_rating_questions.rating_question_id'
                )
                ->pluck('form_rating_questions.rating_question_id');

            $form_open_question_ids = DB::table('form_open_questions')
                ->where('form_id', $form_status->form_id)
                ->join(
                    'open_questions',
                    'open_questions.id',
                    '=',
                    'form_open_questions.open_question_id'
                )
                ->pluck('form_open_questions.open_question_id');

            $supervised_student_user_ids = DB::table('users')
                ->where('role', 'student')
                ->join(
                    'students',
                    'students.student_number',
                    '=',
                    'users.role_id'
                )
                ->where('supervisor_id', $supervisor_id)
                ->pluck('users.id');

            // Generate form items if it doesn't already exist
            foreach ($supervised_student_user_ids as $user_id) {
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

            $form_answers = $form_statuses_partial->get();
        }

        $students = [];
        $rating_categories = $this->getRatingCategories($form_status->form_id);

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
                    ->join(
                        'rating_questions',
                        'rating_questions.id',
                        '=',
                        'rating_scores.rating_question_id'
                    )
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

        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        return Inertia::render('dashboard/(supervisor)/WriteableForm', [
            'students' => $students,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
        ]);
    }

    private function updateSupervisorForm($form_values, string $short_name)
    {
        $supervisor_user = Auth::user();

        $form_id = Form::where('short_name', $short_name)->firstOrFail()->id;

        foreach ($form_values['answers'] as $evaluation) {
            $form_status_id = FormStatus::where('user_id', $supervisor_user->id)
                ->where('form_id', $form_id)
                ->firstOrFail()
                ->id;

            $form_answer = FormAnswer::where('form_status_id', $form_status_id)
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

    public function draftSupervisorForm(Request $request, string $short_name): RedirectResponse
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

        $this->updateSupervisorForm($form_values, $short_name);

        return redirect('/dashboard');
    }

    public function submitSupervisorForm(Request $request, string $short_name): RedirectResponse
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

        $this->updateSupervisorForm($form_values, $short_name);

        $user = Auth::user();

        // Update status for all reports under the supervisor
        FormStatus::where('user_id', $user->id)
            ->join(
                'forms',
                'forms.id',
                '=',
                'form_statuses.form_id'
            )
            ->where('forms.short_name', $short_name)
            ->update(['status' => 'submitted']);

        return redirect('/dashboard');
    }

    public function showReadOnlySupervisorForm(int $supervisor_id, string $short_name)
    {
        $user_id = DB::table('users')
            ->where('role', 'supervisor')
            ->where('role_id', $supervisor_id)
            ->firstOrFail()
            ->id;

        $form_statuses_partial = DB::table('form_statuses')
            ->where('user_id', $user_id)
            ->join(
                'forms',
                'forms.id',
                '=',
                'form_statuses.form_id'
            )
            ->where('short_name', $short_name)
            ->select('form_statuses.form_id', 'form_statuses.id AS form_status_id');

        $form_status = $form_statuses_partial->firstOrFail();

        $form_answers = $form_statuses_partial
            ->join(
                'form_answers',
                'form_answers.form_status_id',
                '=',
                'form_statuses.id'
            )
            ->select(
                'form_statuses.form_id',
                'form_statuses.id AS form_status_id',
                'form_answers.id AS form_answer_id',
                'form_answers.evaluated_user_id'
            )
            ->get();

        $students = [];
        $rating_categories = $this->getRatingCategories($form_status->form_id);

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
                    ->join(
                        'rating_questions',
                        'rating_questions.id',
                        '=',
                        'rating_scores.rating_question_id'
                    )
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

        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        $status = DB::table('form_statuses')
            ->where('user_id', $user_id)
            ->where('form_id', $form_status->form_id)
            ->firstOrFail()
            ->status;

        return Inertia::render('dashboard/(faculty)/supervisors/ReadOnlyForm', [
            'supervisor_id' => $supervisor_id,
            'students' => $students,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
            'status' => $status,
        ]);
    }

    public function showWriteableStudentForm(string $short_name): Response
    {
        $student_user = Auth::user();
        $student_number = $student_user->role_id;
        $supervisor_id = DB::table('students')
            ->where('student_number', $student_number)
            ->firstOrFail()
            ->supervisor_id;

        $form_statuses_partial = DB::table('form_statuses')
            ->where('user_id', $student_user->id)
            ->join(
                'forms',
                'forms.id',
                '=',
                'form_statuses.form_id'
            )
            ->where('short_name', $short_name)
            ->select('form_statuses.form_id', 'form_statuses.id AS form_status_id');

        $form_status = $form_statuses_partial->firstOrFail();

        $form_answer = $form_statuses_partial
            ->join(
                'form_answers',
                'form_answers.form_status_id',
                '=',
                'form_statuses.id'
            )
            ->select(
                'form_statuses.form_id',
                'form_statuses.id AS form_status_id',
                'form_answers.id AS form_answer_id',
                'form_answers.evaluated_user_id'
            )
            ->first();

        if (!$form_answer) {
            // Hard-coded question ids for the "report" form
            $form_rating_question_ids = DB::table('form_rating_questions')
                ->where('form_id', $form_status->form_id)
                ->join(
                    'rating_questions',
                    'rating_questions.id',
                    '=',
                    'form_rating_questions.rating_question_id'
                )
                ->pluck('form_rating_questions.rating_question_id');

            $form_open_question_ids = DB::table('form_open_questions')
                ->where('form_id', $form_status->form_id)
                ->join(
                    'open_questions',
                    'open_questions.id',
                    '=',
                    'form_open_questions.open_question_id'
                )
                ->pluck('form_open_questions.open_question_id');

            $supervisor_user_id = DB::table('users')
                ->where('role', 'supervisor')
                ->where('role_id', $supervisor_id)
                ->firstOrFail()
                ->id;

            // Generate form items if it doesn't already exist
            $new_form_answer = new FormAnswer();
            $new_form_answer->form_status_id = $form_status->form_status_id;
            $new_form_answer->evaluated_user_id = $supervisor_user_id;
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

            $form_answer = $form_statuses_partial->first();
        }

        $rating_categories = $this->getRatingCategories($form_status->form_id);

        $form_answer_id = $form_answer->form_answer_id;
        $supervisor_user_id = $form_answer->evaluated_user_id;

        $supervisor_info = DB::table('users')
            ->where('id', $supervisor_user_id)
            ->select('first_name', 'last_name', 'role_id AS supervisor_id')
            ->firstOrFail();

        $categorized_ratings = [];
        foreach ($rating_categories as $category) {
            $ratings = DB::table('rating_scores')
                ->where('form_answer_id', $form_answer_id)
                ->join(
                    'rating_questions',
                    'rating_questions.id',
                    '=',
                    'rating_scores.rating_question_id'
                )
                ->where('rating_questions.rating_category_id', $category->id)
                ->pluck('rating_scores.score', 'rating_scores.rating_question_id')
                ->toArray();

            $categorized_ratings[$category->id] = $ratings;
        }

        $opens = DB::table('open_answers')
            ->where('form_answer_id', $form_answer_id)
            ->pluck('answer', 'open_question_id')
            ->toArray();

        $supervisor = [
            'supervisor_id' => $supervisor_info->supervisor_id,
            'last_name' => $supervisor_info->last_name,
            'first_name' => $supervisor_info->first_name,
            'categorized_ratings' => $categorized_ratings,
            'opens' => $opens,
        ];

        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        return Inertia::render('dashboard/(student)/WriteableForm', [
            'supervisor' => $supervisor,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
        ]);
    }

    private function updateStudentForm($form_values, string $short_name)
    {
        $student_user = Auth::user();

        $form_id = Form::where('short_name', $short_name)->firstOrFail()->id;

        foreach ($form_values['answers'] as $evaluation) {
            $form_status_id = FormStatus::where('user_id', $student_user->id)
                ->where('form_id', $form_id)
                ->firstOrFail()
                ->id;

            $form_answer = FormAnswer::where('form_status_id', $form_status_id)
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

    public function draftStudentForm(Request $request, string $short_name): RedirectResponse
    {
        $form_values = $request->validate([
            'answers' => ['array'],
            'answers.*.supervisor_id' => ['integer', 'numeric'],
            'answers.*.categorized_ratings' => ['array'],
            'answers.*.categorized_ratings.*' => ['array'],
            'answers.*.categorized_ratings.*.*' => ['nullable'],
            'answers.*.opens' => ['array'],
            'answers.*.opens.*' => ['nullable'],
        ]);

        $this->updateStudentForm($form_values, $short_name);

        return redirect('/dashboard');
    }

    public function submitStudentForm(Request $request, string $short_name): RedirectResponse
    {
        $form_values = $request->validate([
            'answers' => ['array'],
            'answers.*.supervisor_id' => ['integer', 'numeric'],
            'answers.*.categorized_ratings' => ['array'],
            'answers.*.categorized_ratings.*' => ['array'],
            'answers.*.categorized_ratings.*.*' => ['required', 'integer', 'numeric'],
            'answers.*.opens' => ['array'],
            'answers.*.opens.*' => ['nullable', 'string'],
        ]);

        $this->updateStudentForm($form_values, $short_name);

        $user = Auth::user();

        // Update status for all reports under the supervisor
        FormStatus::where('user_id', $user->id)
            ->join(
                'forms',
                'forms.id',
                '=',
                'form_statuses.form_id'
            )
            ->where('forms.short_name', $short_name)
            ->update(['status' => 'submitted']);

        return redirect('/dashboard');
    }

    public function showReadOnlyStudentForm(int $supervisor_id, string $short_name)
    {
        $user_id = DB::table('users')
            ->where('role', 'supervisor')
            ->where('role_id', $supervisor_id)
            ->firstOrFail()
            ->id;

        $form_statuses_partial = DB::table('form_statuses')
            ->where('user_id', $user_id)
            ->join(
                'forms',
                'forms.id',
                '=',
                'form_statuses.form_id'
            )
            ->where('short_name', $short_name)
            ->select('form_statuses.form_id', 'form_statuses.id AS form_status_id');

        $form_status = $form_statuses_partial->firstOrFail();

        $form_answers = $form_statuses_partial
            ->join(
                'form_answers',
                'form_answers.form_status_id',
                '=',
                'form_statuses.id'
            )
            ->select(
                'form_statuses.form_id',
                'form_statuses.id AS form_status_id',
                'form_answers.id AS form_answer_id',
                'form_answers.evaluated_user_id'
            )
            ->get();

        $students = [];
        $rating_categories = $this->getRatingCategories($form_status->form_id);

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
                    ->join(
                        'rating_questions',
                        'rating_questions.id',
                        '=',
                        'rating_scores.rating_question_id'
                    )
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

        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        $status = DB::table('form_statuses')
            ->where('user_id', $user_id)
            ->where('form_id', $form_status->form_id)
            ->firstOrFail()
            ->status;

        return Inertia::render('dashboard/(faculty)/supervisors/ReadOnlyForm', [
            'supervisor_id' => $supervisor_id,
            'students' => $students,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
            'status' => $status,
        ]);
    }










    public function validateForm(int $user_id, $short_name): RedirectResponse
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

    public function invalidateForm(int $user_id, $short_name): RedirectResponse
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

    public function rejectForm(int $user_id, $short_name): RedirectResponse
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
