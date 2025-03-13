<?php

namespace App\Http\Controllers;

use App\Models\FormAnswer;
use App\Models\OpenAnswer;
use App\Models\RatingScore;
use App\Models\User;
use App\Models\WebsiteState;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function show(Request $request): Response
    {
        $phase = WebsiteState::findOrFail(1)->phase;

        switch (Auth::user()->role) {
            case User::ROLE_STUDENT:
                return $this->showStudentDashboard($request, $phase);
            case User::ROLE_SUPERVISOR:
                return $this->showSupervisorDashboard($request, $phase);
            case User::ROLE_FACULTY:
                return $this->showFacultyDashboard($request, $phase);
            case User::ROLE_ADMIN:
                return $this->showAdminDashboard($request, $phase);
        }

        // Error 401 since the user does not have an existing role
        abort(401);
    }

    private function showStudentDashboard(Request $request, string $phase): Response
    {
        $props = [];

        switch ($phase) {
            case 'pre':
                $student_id = Auth::user()->role_id;

                $submission_statuses = DB::table('submission_statuses')
                    ->where('student_id', $student_id)
                    ->join('requirements', 'submission_statuses.requirement_id', '=', 'requirements.id')
                    ->where(function ($query) {
                        $query->whereRaw("requirements.deadline > STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')", Carbon::now('Asia/Manila')->format('Y-m-d H:i'))
                            ->orWhereNull('requirements.deadline');
                    })
                    ->select(
                        'requirements.id AS requirement_id',
                        'requirements.requirement_name',
                        'requirements.deadline',
                        'submission_statuses.status',
                    )
                    ->get();

                $props = [
                    'student_id' => $student_id,
                    'submissions' => $submission_statuses,
                ];

                return Inertia::render('dashboard/(student)/RequirementsDashboard', $props);
            case 'during':
            case 'post':
                $student_user = Auth::user();
                $student_user_id = $student_user->id;
                $student_id = $student_user->role_id;

                $student = DB::table('students')
                    ->where('id', $student_id)
                    ->firstOrFail();

                if (!$student->faculty_id) {
                    return Inertia::render('dashboard/(student)/Unenrolled');
                }

                $form_statuses = DB::table('form_statuses')
                    ->where('user_id', $student_user_id)
                    ->join(
                        'forms',
                        'forms.id',
                        '=',
                        'form_statuses.form_id'
                    )
                    ->where('forms.phase', $phase)
                    ->where(function ($query) {
                        $query->whereRaw("forms.deadline > STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')", Carbon::now('Asia/Manila')->format('Y-m-d H:i'))
                            ->orWhereNull('forms.deadline');
                    })
                    ->select('forms.form_name', 'forms.short_name', 'form_statuses.status', 'forms.deadline')
                    ->get();

                $props = [
                    'student_id' => $student_id,
                    'form_statuses' => $form_statuses,
                ];

                return Inertia::render('dashboard/(student)/FormsDashboard', $props);
            default:
                return Inertia::render('dashboard/WaitingPage');
        }
    }

    private function showSupervisorDashboard(Request $request, string $phase): Response
    {
        $props = [];
        $supervisor_user = Auth::user();

        switch ($phase) {
            case 'during':
                $supervisor = Auth::user();
                $supervisor_user_id = $supervisor->id;
                $supervisor_id = $supervisor->role_id;

                $company_name = DB::table('supervisors')
                    ->where('supervisors.id', $supervisor_id)
                    ->join('companies', 'supervisors.company_id', '=', 'companies.id')
                    ->select('companies.company_name')
                    ->firstOrFail()
                    ->company_name;

                $form_statuses = DB::table('form_statuses')
                    ->where('user_id', $supervisor_user_id)
                    ->join(
                        'forms',
                        'forms.id',
                        '=',
                        'form_statuses.form_id'
                    )
                    ->where('forms.phase', $phase)
                    ->where(function ($query) {
                        $query->whereRaw("forms.deadline > STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')", Carbon::now('Asia/Manila')->format('Y-m-d H:i'))
                            ->orWhereNull('forms.deadline');
                    })
                    ->select('forms.form_name', 'forms.short_name', 'form_statuses.status', 'forms.deadline')
                    ->get();

                $props = [
                    'phase' => $phase,
                    'students' => null,
                    'company_name' => $company_name,
                    'form_statuses' => $form_statuses,
                ];

                return Inertia::render('dashboard/(supervisor)/FormsDashboard', $props);
            case 'post':
                $supervisor = Auth::user();
                $supervisor_user_id = $supervisor->id;
                $supervisor_id = $supervisor->role_id;

                $company_name = DB::table('supervisors')
                    ->where('supervisors.id', $supervisor_id)
                    ->join('companies', 'supervisors.company_id', '=', 'companies.id')
                    ->select('companies.company_name')
                    ->firstOrFail()
                    ->company_name;

                $form_statuses = DB::table('form_statuses')
                    ->where('user_id', $supervisor_user_id)
                    ->join(
                        'forms',
                        'forms.id',
                        '=',
                        'form_statuses.form_id'
                    )
                    ->where('forms.phase', $phase)
                    ->where(function ($query) {
                        $query->whereRaw("forms.deadline > STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')", Carbon::now('Asia/Manila')->format('Y-m-d H:i'))
                            ->orWhereNull('forms.deadline');
                    })
                    ->select('forms.form_name', 'forms.short_name', 'form_statuses.status', 'forms.deadline')
                    ->get();

                $ungenerated_self_evaluation_statuses = DB::table('users')
                    ->where('role', 'student')
                    ->join('students', 'students.id', '=', 'users.role_id')
                    ->where('supervisor_id', $supervisor_user->role_id)
                    ->join('form_statuses', 'form_statuses.user_id', '=', 'users.id')
                    ->where('form_id', 4)
                    ->leftJoin('form_answers', 'form_answers.form_status_id', '=', 'form_statuses.id')
                    ->whereNull('form_answers.id')
                    ->select('form_statuses.form_id', 'form_statuses.id AS form_status_id')
                    ->get();

                $form_rating_question_ids = DB::table('form_rating_questions')
                    ->where('form_id', 4)
                    ->join('rating_questions', 'rating_questions.id', '=', 'form_rating_questions.rating_question_id')
                    ->pluck('form_rating_questions.rating_question_id');

                $form_open_question_ids = DB::table('form_open_questions')
                    ->where('form_id', 4)
                    ->join('open_questions', 'open_questions.id', '=', 'form_open_questions.open_question_id')
                    ->pluck('form_open_questions.open_question_id');

                foreach ($ungenerated_self_evaluation_statuses as $form_status) {
                    // Generate form items if it doesn't already exist
                    $new_form_answer = new FormAnswer();
                    $new_form_answer->form_status_id = $form_status->form_status_id;
                    $new_form_answer->evaluated_user_id = null;
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

                $supervised_self_evaluation_statuses = DB::table('users')
                    ->where('role', 'student')
                    ->join('students', 'students.id', '=', 'users.role_id')
                    ->where('supervisor_id', $supervisor_user->role_id)
                    ->join('form_statuses', 'form_statuses.user_id', '=', 'users.id')
                    ->where('form_id', 4)
                    ->leftJoin('form_answers', 'form_answers.form_status_id', '=', 'form_statuses.id')
                    ->select('users.id AS user_id', 'form_statuses.form_id', 'form_statuses.id AS form_status_id')
                    ->get();

                $students = [];

                foreach ($supervised_self_evaluation_statuses as $form_status) {
                    $student_info = DB::table('users')
                        ->where('users.id', $form_status->user_id)
                        ->join('students', 'students.id', '=', 'users.role_id')
                        ->select('users.id AS user_id', 'students.id AS student_id', 'first_name', 'last_name', 'student_number')
                        ->firstOrFail();

                    $total_hours = DB::table('form_statuses')
                        ->where('user_id', $form_status->user_id)
                        ->join('form_answers', 'form_answers.form_status_id', '=', 'form_statuses.id')
                        ->join('rating_scores', 'rating_scores.form_answer_id', '=', 'form_answers.id')
                        ->where('rating_question_id', 14) // 14 = Total hours rating question
                        ->firstOrFail()
                        ->score;

                    $self_assessment_status = DB::table('form_statuses')
                        ->where('user_id', $form_status->user_id)
                        ->where('form_id', 4) // 4 = Intern self-evaluation form
                        ->firstOrFail()
                        ->status;

                    $students[$student_info->student_id] = [
                        'student_user_id' => $student_info->user_id,
                        'student_number' => $student_info->student_number,
                        'last_name' => $student_info->last_name,
                        'first_name' => $student_info->first_name,
                        'total_hours' => $total_hours,
                        'self_assessment_status' => $self_assessment_status,
                    ];
                }

                $props = [
                    'phase' => $phase,
                    'students' => $students,
                    'company_name' => $company_name,
                    'form_statuses' => $form_statuses,
                ];

                return Inertia::render('dashboard/(supervisor)/FormsDashboard', $props);
            default:
                return Inertia::render('dashboard/WaitingPage');
        }
    }

    private function showFacultyDashboard(Request $request, string $phase): Response
    {
        $requirements = DB::table('requirements')->get();
        $forms = DB::table('forms')->get();

        return Inertia::render('dashboard/(faculty)/Index', [
            'currentPhase' => $phase,
            'requirements' => $requirements,
            'forms' => $forms,
        ]);
    }

    private function showAdminDashboard(Request $request, string $phase): Response
    {
        $requirements = DB::table('requirements')->get();
        $forms = DB::table('forms')->get();

        return Inertia::render('dashboard/(admin)/Index', [
            'currentPhase' => $phase,
            'requirements' => $requirements,
            'forms' => $forms,
        ]);
    }
}
