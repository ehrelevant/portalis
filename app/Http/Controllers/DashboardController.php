<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\SubmissionStatus;
use App\Models\User;
use App\Models\WebsiteState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                abort(404);
        }

        // Error 401 since the user does not have an existing role
        abort(401);
    }

    private function showStudentDashboard(Request $request, string $phase): Response
    {
        $props = [];

        switch ($phase) {
            case 'pre':
                $student_number = Auth::user()->role_id;

                $submission_statuses = DB::table('submission_statuses')
                    ->where('student_number', $student_number)
                    ->join('requirements', 'submission_statuses.requirement_id', '=', 'requirements.id')
                    ->select(
                        'requirements.id AS requirement_id',
                        'requirements.requirement_name',
                        'requirements.deadline',
                        'submission_statuses.status',
                    )
                    ->get();

                $props = [
                    'student_number' => $student_number,
                    'submissions' => $submission_statuses,
                ];

                return Inertia::render('dashboard/(student)/RequirementsDashboard', $props);
            case 'during':
            case 'post':
                $student = Auth::user();
                $student_user_id = $student->id;
                $student_number = $student->role_id;

                $form_statuses = DB::table('form_statuses')
                    ->where('user_id', $student_user_id)
                    ->join(
                        'forms',
                        'forms.id',
                        '=',
                        'form_statuses.form_id'
                    )
                    ->where('forms.phase', $phase)
                    ->select('forms.form_name', 'forms.short_name', 'form_statuses.status', 'forms.deadline')
                    ->get();

                $props = [
                    'student_number' => $student_number,
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

        switch ($phase) {
            case 'during':
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
                    ->select('forms.form_name', 'forms.short_name', 'form_statuses.status', 'forms.deadline')
                    ->get();

                $props = [
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
}
