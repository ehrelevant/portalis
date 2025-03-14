<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormAnswer;
use App\Models\FormStatus;
use App\Models\OpenAnswer;
use App\Models\RatingScore;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

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
                    'rating_questions.criterion',
                    'rating_questions.tooltip'
                )
                ->get();

            foreach ($rating_questions as $rating_question) {
                $categorized_rating_questions[$category->id][$rating_question->rating_question_id] =
                    [
                        'criterion' => $rating_question->criterion,
                        'min_score' =>  $rating_question->min_score,
                        'max_score' =>  $rating_question->max_score,
                        'tooltip' => $rating_question->tooltip,
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
            ->select('form_statuses.form_id', 'form_statuses.id AS form_status_id', 'form_statuses.status');
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

    // Form Redirects
    public function answerForm(string $short_name, ?int $role_id = null)
    {
        $user = Auth::user();

        switch ($short_name) {
            case 'midsem':
            case 'final':
            case 'intern-evaluation':
                if ($user->role === User::ROLE_SUPERVISOR) {
                    if ($role_id && $role_id != $user->role_id) {
                        abort(401);
                    } elseif (!$role_id) {
                        $role_id = $user->role_id;
                    }
                } elseif ($user->role === User::ROLE_ADMIN) {
                    if (!$role_id) {
                        abort(404);
                    }
                } else {
                    abort(401);
                }

                return $this->answerSupervisorForm($short_name, $role_id);
            case 'company-evaluation':
            case 'self-evaluation':
                if ($user->role === User::ROLE_STUDENT) {
                    if ($role_id && $role_id != $user->role_id) {
                        abort(401);
                    } elseif (!$role_id) {
                        $role_id = $user->role_id;
                    }
                } elseif ($user->role === User::ROLE_ADMIN) {
                    if (!$role_id) {
                        abort(404);
                    }
                } else {
                    abort(401);
                }

                return $this->answerStudentForm($short_name, $role_id);
            default:
                abort(404);
        }
    }
    public function draftForm(Request $request, string $short_name, ?int $role_id = null)
    {
        $user = Auth::user();

        switch ($short_name) {
            case 'midsem':
            case 'final':
            case 'intern-evaluation':
                if ($user->role === User::ROLE_SUPERVISOR) {
                    if ($role_id && $role_id != $user->role_id) {
                        abort(401);
                    } elseif (!$role_id) {
                        $role_id = $user->role_id;
                    }
                } elseif ($user->role === User::ROLE_ADMIN) {
                    if (!$role_id) {
                        abort(404);
                    }
                } else {
                    abort(401);
                }

                return $this->draftSupervisorForm($request, $short_name, $role_id);
            case 'company-evaluation':
            case 'self-evaluation':
                if ($user->role === User::ROLE_STUDENT) {
                    if ($role_id && $role_id != $user->role_id) {
                        abort(401);
                    } elseif (!$role_id) {
                        $role_id = $user->role_id;
                    }
                } elseif ($user->role === User::ROLE_ADMIN) {
                    if (!$role_id) {
                        abort(404);
                    }
                } else {
                    abort(401);
                }

                return $this->draftStudentForm($request, $short_name, $role_id);
            default:
                abort(404);
        }
    }
    public function submitForm(Request $request, string $short_name, ?int $role_id = null)
    {
        $user = Auth::user();

        switch ($short_name) {
            case 'midsem':
            case 'final':
            case 'intern-evaluation':
                if ($user->role === User::ROLE_SUPERVISOR) {
                    if ($role_id && $role_id != $user->role_id) {
                        abort(401);
                    } elseif (!$role_id) {
                        $role_id = $user->role_id;
                    }
                } elseif ($user->role === User::ROLE_ADMIN) {
                    if (!$role_id) {
                        abort(404);
                    }
                } else {
                    abort(401);
                }

                return $this->submitSupervisorForm($request, $short_name, $role_id);
            case 'company-evaluation':
            case 'self-evaluation':
                if ($user->role === User::ROLE_STUDENT) {
                    if ($role_id && $role_id != $user->role_id) {
                        abort(401);
                    } elseif (!$role_id) {
                        $role_id = $user->role_id;
                    }
                } elseif ($user->role === User::ROLE_ADMIN) {
                    if (!$role_id) {
                        abort(404);
                    }
                } else {
                    abort(401);
                }

                return $this->submitStudentForm($request, $short_name, $role_id);
            default:
                abort(404);
        }
    }
    public function viewForm(string $short_name, int $role_id)
    {
        switch ($short_name) {
            case 'midsem':
            case 'final':
            case 'intern-evaluation':
                return $this->viewSupervisorForm($short_name, $role_id);
            case 'company-evaluation':
            case 'self-evaluation':
                return $this->viewStudentForm($short_name, $role_id);
            default:
                abort(404);
        }
    }

    // Supervisor Form Functions
    public function getSavedSupervisorFormValues($form_answers, $rating_categories)
    {
        $students = [];

        foreach ($form_answers as $form_answer) {
            $form_answer_id = $form_answer->form_answer_id;
            $student_user_id = $form_answer->evaluated_user_id;

            $student_info = DB::table('users')
                ->where('id', $student_user_id)
                ->select('first_name', 'last_name', 'role_id AS student_id')
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

            $students[$student_info->student_id] = [
                'student_id' => $student_info->student_id,
                'last_name' => $student_info->last_name,
                'first_name' => $student_info->first_name,
                'categorized_ratings' => $categorized_ratings,
                'opens' => $opens,
            ];
        }

        return $students;
    }

    public function answerSupervisorForm(string $short_name, ?int $supervisor_id)
    {
        $supervisor_user = Auth::user();
        if ($supervisor_user->role === User::ROLE_ADMIN) {
            $supervisor_user = DB::table('users')
                ->where('role', 'supervisor')
                ->where('role_id', $supervisor_id)
                ->firstOrFail();
        }

        $form_status = $this->queryFormStatus($supervisor_user->id, $short_name)->firstOrFail();
        $form_answers = $this->queryFormAnswers($supervisor_user->id, $short_name)->get();

        if ($form_answers->isEmpty()) {
            $supervised_student_user_ids = DB::table('users')
                ->where('role', 'student')
                ->join('students', 'students.id', '=', 'users.role_id')
                ->where('supervisor_id', $supervisor_user->role_id)
                ->pluck('users.id');

            $this->createForm($form_status, $supervised_student_user_ids);
            $form_answers = $this->queryFormAnswers($supervisor_user->id, $short_name)->get();
        }

        $rating_categories = $this->getRatingCategories($form_status->form_id);
        $students = $this->getSavedSupervisorFormValues($form_answers, $rating_categories);
        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        return Inertia::render('form/supervisor/Answer', [
            'supervisor' => $supervisor_user,
            'students' => $students,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,

            'isAdmin' => (Auth::user()->role === User::ROLE_ADMIN),
            'evaluatorUserId' => $supervisor_user->id,
            'evaluatorRoleId' => $supervisor_user->role_id,
            'status' => $form_status->status,
        ]);
    }

    public function viewSupervisorForm(string $short_name, int $supervisor_id)
    {
        $supervisor_user = DB::table('users')
            ->where('role', 'supervisor')
            ->where('role_id', $supervisor_id)
            ->firstOrFail();

        $user_id = $supervisor_user->id;

        $form_status = $this->queryFormStatus($user_id, $short_name)->firstOrFail();
        $form_answers = $this->queryFormAnswers($user_id, $short_name)->get();

        $rating_categories = $this->getRatingCategories($form_status->form_id);
        $students = $this->getSavedSupervisorFormValues($form_answers, $rating_categories);
        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        $status = DB::table('form_statuses')
            ->where('user_id', $user_id)
            ->where('form_id', $form_status->form_id)
            ->firstOrFail()
            ->status;

        return Inertia::render('form/supervisor/View', [
            'supervisor' => $supervisor_user,
            'evaluator_user_id' => $user_id,
            'students' => $students,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
            'status' => $status,
        ]);
    }

    public function updateSupervisorForm($form_values, string $short_name, $supervisor_user)
    {
        $form_id = Form::where('short_name', $short_name)->firstOrFail()->id;

        foreach ($form_values['answers'] as $evaluation) {
            $form_status_id = FormStatus::where('user_id', $supervisor_user->id)
                ->where('form_id', $form_id)
                ->firstOrFail()
                ->id;

            $form_answer = FormAnswer::where('form_status_id', $form_status_id)
                ->join('users', 'users.id', '=', 'form_answers.evaluated_user_id')
                ->where('role', 'student')
                ->where('role_id', $evaluation['student_id'])
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

    public function draftSupervisorForm(Request $request, string $short_name, ?int $supervisor_id)
    {
        try {
            $supervisor_user = Auth::user();
            if ($supervisor_user->role === User::ROLE_ADMIN) {
                $supervisor_user = DB::table('users')
                    ->where('role', 'supervisor')
                    ->where('role_id', $supervisor_id)
                    ->firstOrFail();
            }

            $form_values = $request->validate([
                'answers' => ['array'],
                'answers.*.student_id' => ['integer', 'numeric'],
                'answers.*.categorized_ratings' => ['array'],
                'answers.*.categorized_ratings.*' => ['array'],
                'answers.*.categorized_ratings.*.*' => ['nullable'],
                'answers.*.opens' => ['array'],
                'answers.*.opens.*' => ['nullable'],
            ]);

            $this->updateSupervisorForm($form_values, $short_name, $supervisor_user);

            if (Auth::user()->role === User::ROLE_ADMIN) {
                return redirect('/dashboard/admin/students')->with('success', 'Successfully drafted the form. This tab may now be closed.');
            } else {
                return redirect('/dashboard')->with('success', 'Successfully drafted the form. This tab may now be closed.');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Failed to draft form.');
        }
    }

    public function submitSupervisorForm(Request $request, string $short_name, ?int $supervisor_id)
    {
        try {
            $supervisor_user = Auth::user();
            if ($supervisor_user->role === User::ROLE_ADMIN) {
                $supervisor_user = DB::table('users')
                    ->where('role', 'supervisor')
                    ->where('role_id', $supervisor_id)
                    ->firstOrFail();
            }

            $form_values = $request->validate([
                'answers' => ['array'],
                'answers.*.student_id' => ['integer', 'numeric'],
                'answers.*.categorized_ratings' => ['array'],
                'answers.*.categorized_ratings.*' => ['array'],
                'answers.*.categorized_ratings.*.*' => ['required', 'integer', 'numeric'],
                'answers.*.opens' => ['array'],
                'answers.*.opens.*' => ['nullable', 'string'],
            ]);

            $this->updateSupervisorForm($form_values, $short_name, $supervisor_user);

            // Update status for all reports under the supervisor
            FormStatus::where('user_id', $supervisor_user->id)
                ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
                ->where('forms.short_name', $short_name)
                ->update(['status' => 'For Review']);

            if (Auth::user()->role === User::ROLE_ADMIN) {
                return redirect('/dashboard/admin/students')->with('success', 'Successfully submitted the form. This tab may now be closed.');
            } else {
                return redirect('/dashboard')->with('success', 'Successfully submitted the form. This tab may now be closed.');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Failed to submit form.');
        }
    }

    // Company Evaluation Forms
    private function getSavedStudentFormValues($form_answer, $rating_categories)
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

    public function answerStudentForm(string $short_name, ?int $student_id)
    {
        $student_user = Auth::user();
        if ($student_user->role === User::ROLE_ADMIN) {
            $student_user = DB::table('users')
                ->where('role', 'student')
                ->where('role_id', $student_id)
                ->firstOrFail();
        }

        $form_status = $this->queryFormStatus($student_user->id, $short_name)->firstOrFail();
        $form_answer = $this->queryFormAnswers($student_user->id, $short_name)->first();

        if (!$form_answer) {
            $this->createForm($form_status, [null]);
            $form_answer = $this->queryFormAnswers($student_user->id, $short_name)->first();
        }

        $rating_categories = $this->getRatingCategories($form_status->form_id);
        $values = $this->getSavedStudentFormValues($form_answer, $rating_categories);
        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        return Inertia::render('form/student/Answer', [
            'student' => $student_user,
            'values' => $values,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,

            'isAdmin' => (Auth::user()->role === User::ROLE_ADMIN),
            'evaluatorUserId' => $student_user->id,
            'evaluatorRoleId' => $student_user->role_id,
            'status' => $form_status->status,
        ]);
    }

    public function viewStudentForm(string $short_name, int $student_id)
    {
        $student_user = DB::table('users')
            ->where('role', 'student')
            ->where('role_id', $student_id)
            ->firstOrFail();

        $user_id = $student_user->id;

        $form_status = $this->queryFormStatus($user_id, $short_name)->firstOrFail();
        $form_answer = $this->queryFormAnswers($user_id, $short_name)->firstOrFail();

        $rating_categories = $this->getRatingCategories($form_status->form_id);
        $values = $this->getSavedStudentFormValues($form_answer, $rating_categories);
        $categorized_rating_questions = $this->getCategorizedRatingQuestions($form_status->form_id, $rating_categories);
        $open_questions = $this->getOpenQuestions($form_status->form_id);
        $form_info = $this->getFormInfo($form_status->form_id);

        $status = DB::table('form_statuses')
            ->where('user_id', $user_id)
            ->where('form_id', $form_status->form_id)
            ->firstOrFail()
            ->status;

        return Inertia::render('form/student/View', [
            'student' => $student_user,
            'evaluator_user_id' => $user_id,
            'values' => $values,
            'rating_categories' => $rating_categories,
            'categorized_rating_questions' => $categorized_rating_questions,
            'open_questions' => $open_questions,
            'form_info' => $form_info,
            'status' => $status,
        ]);
    }

    public function updateStudentForm($evaluation, string $short_name, $student_user)
    {
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

    public function draftStudentForm(Request $request, string $short_name, ?int $student_id)
    {
        try {
            $student_user = Auth::user();
            if ($student_user->role === User::ROLE_ADMIN) {
                $student_user = DB::table('users')
                    ->where('role', 'student')
                    ->where('role_id', $student_id)
                    ->firstOrFail();
            }

            $form_values = $request->validate([
                'categorized_ratings' => ['array'],
                'categorized_ratings.*' => ['array'],
                'categorized_ratings.*.*' => ['nullable'],
                'opens' => ['array'],
                'opens.*' => ['nullable'],
            ]);

            $this->updateStudentForm($form_values, $short_name, $student_user);

            if (Auth::user()->role === User::ROLE_ADMIN) {
                return redirect('/dashboard/admin/students')->with('success', 'Successfully drafted the form. This tab may now be closed.');
            } else {
                return redirect('/dashboard')->with('success', 'Successfully drafted the form. This tab may now be closed.');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Failed to draft form.');
        }
    }

    public function submitStudentForm(Request $request, string $short_name, ?int $student_id)
    {
        try {
            $student_user = Auth::user();
            if ($student_user->role === User::ROLE_ADMIN) {
                $student_user = DB::table('users')
                    ->where('role', 'student')
                    ->where('role_id', $student_id)
                    ->firstOrFail();
            }

            $form_values = $request->validate([
                'categorized_ratings' => ['array'],
                'categorized_ratings.*' => ['array'],
                'categorized_ratings.*.*' => ['required', 'integer', 'numeric'],
                'opens' => ['array'],
                'opens.*' => ['nullable', 'string'],
            ]);

            $this->updateStudentForm($form_values, $short_name, $student_user);

            // Update status for all reports under the supervisor
            FormStatus::where('user_id', $student_user->id)
                ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
                ->where('forms.short_name', $short_name)
                ->update(['status' => 'For Review']);

            if (Auth::user()->role === User::ROLE_ADMIN) {
                return redirect('/dashboard/admin/students')->with('success', 'Successfully submitted the form. This tab may now be closed.');
            } else {
                return redirect('/dashboard')->with('success', 'Successfully submitted the form. This tab may now be closed.');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Failed to submit form.');
        }
    }

    public function validateForm(string $short_name, int $user_id): RedirectResponse
    {
        try {
            $validator_user = Auth::user();

            if (!(in_array($validator_user->role, [User::ROLE_ADMIN, User::ROLE_FACULTY]) || ($validator_user->role == User::ROLE_SUPERVISOR && $short_name === 'self-evaluation'))) {
                abort(401);
            }

            $form_id = Form::where('short_name', $short_name)->firstOrFail()->id;

            $form_status = FormStatus::where('user_id', $user_id)
                ->where('form_id', $form_id)
                ->firstOrFail();

            if ($form_status->status === 'For Review') {
                FormStatus::where('user_id', $user_id)
                    ->where('form_id', $form_id)
                    ->update(['status' => 'Accepted']);
            }

            $success_message = 'Successfully validated the form submission. This tab may now be closed.';
            if ($validator_user->role === User::ROLE_ADMIN) {
                return redirect('/dashboard/admin/students')->with('success', $success_message);
            } else {
                return redirect('/dashboard/students')->with('success', $success_message);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Failed to validate the form submission.');
        }
    }

    public function invalidateForm(string $short_name, int $user_id): RedirectResponse
    {
        try {
            $validator_user = Auth::user();

            if (!(in_array($validator_user->role, [User::ROLE_ADMIN, User::ROLE_FACULTY]) || ($validator_user->role == User::ROLE_SUPERVISOR && $short_name === 'self-evaluation'))) {
                abort(401);
            }

            $form_id = Form::where('short_name', $short_name)->firstOrFail()->id;

            $form_status = FormStatus::where('user_id', $user_id)
                ->where('form_id', $form_id)
                ->firstOrFail();

            if ($form_status->status === 'Accepted') {
                FormStatus::where('user_id', $user_id)
                    ->where('form_id', $form_id)
                    ->update(['status' => 'For Review']);
            }

            $success_message = 'Successfully invalidated the form submission. This tab may now be closed.';
            if ($validator_user->role === User::ROLE_ADMIN) {
                return redirect('/dashboard/admin/students')->with('success', $success_message);
            } else {
                return redirect('/dashboard/students')->with('success', $success_message);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Failed to invalidate the form submission.');
        }
    }

    public function rejectForm(string $short_name, int $user_id): RedirectResponse
    {
        try {
            $validator_user = Auth::user();

            if (!(in_array($validator_user->role, [User::ROLE_ADMIN, User::ROLE_FACULTY]) || ($validator_user->role == User::ROLE_SUPERVISOR && $short_name === 'self-evaluation'))) {
                abort(401);
            }

            $form_id = Form::where('short_name', $short_name)->firstOrFail()->id;

            $form_status = FormStatus::where('user_id', $user_id)
                ->where('form_id', $form_id)
                ->firstOrFail();

            if ($form_status->status === 'For Review') {
                FormStatus::where('user_id', $user_id)
                    ->where('form_id', $form_id)
                    ->update(['status' => 'Returned']);
            }

            $success_message = 'Successfully rejected the form submission. This tab may now be closed.';
            if ($validator_user->role === User::ROLE_ADMIN) {
                return redirect('/dashboard/admin/students')->with('success', $success_message);
            } else {
                return redirect('/dashboard/students')->with('success', $success_message);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Failed to reject the form submission.');
        }
    }
}
