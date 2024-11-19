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
    public function redirectPhase(): RedirectResponse
    {
        $phase = WebsiteState::findOrFail(1)->phase;
        return redirect('/dashboard/' . $phase);
    }

    public function show(string $phase, Request $request): Response
    {
        switch (Auth::user()->role) {
            case User::ROLE_STUDENT:
                return Inertia::render('dashboard/' . $phase . '/(student)/Index');
            case User::ROLE_SUPERVISOR:
                return $this->showSupervisor($phase);
            case User::ROLE_FACULTY:
                return $this->showFaculty($phase, $request);
            case User::ROLE_ADMIN:
                return Inertia::render('dashboard/' . $phase . '/(admin)/Index');
        }

        abort(404);
    }

    public function showSupervisor(string $phase): Response
    {
        if ($phase === 'during') {
            $supervisor_id = Auth::user()->role_id;
            $company_name = DB::table('supervisors')
                ->where('supervisors.id', $supervisor_id)
                ->join('companies', 'supervisors.company_id', '=', 'companies.id')
                ->select('companies.company_name')
                ->firstOrFail()
                ->company_name;

            $weekly_report_statuses = DB::table('weekly_report_statuses')
                ->where('supervisor_id', $supervisor_id)
                ->select(
                    'week',
                    'status'
                )
                ->distinct()
                ->get();

            /*
             * This might have issues if for some reason two interns under the
             * same supervisor do not have the same status, which should never happen.
             */
            $intern_evaluation_status = DB::table('intern_evaluation_statuses')
                ->where('supervisor_id', $supervisor_id)
                ->select('status')
                ->firstOrFail()
                ->status;

            return Inertia::render('dashboard/' . $phase . '/(supervisor)/Index', [
                'company_name' => $company_name,
                'weekly_report_statuses' => $weekly_report_statuses,
                'intern_evaluation_status' => $intern_evaluation_status,
            ]);
        }

        return Inertia::render('dashboard/' . $phase . '/(supervisor)/Index');
    }

    public function showFaculty(string $phase, Request $request): Response
    {
        if ($phase === 'pre') {
            $faculty_user = Auth::user();

            $search_text = $request->query('search') ?? '';

            // TODO: Add student number search
            $students_partial = DB::table('students')
                ->where('supervisor_id', $faculty_user->role_id)
                ->where('first_name', 'LIKE', '%' . $search_text . '%')
                ->orWhere('last_name', 'LIKE', '%' . $search_text . '%')
                ->orWhere('middle_name', 'LIKE', '%' . $search_text . '%');

            $students_data = $students_partial
                ->join('users', 'students.student_number', '=', 'users.role_id')
                ->join('submission_statuses', 'students.student_number', '=', 'submission_statuses.student_number')
                ->where('role', 'student')
                ->select(
                    'students.student_number',
                    'users.first_name',
                    'users.middle_name',
                    'users.last_name',
                    DB::raw("MIN(submission_statuses.status) AS total_status")
                )
                ->groupBy(
                    'students.student_number',
                    'users.first_name',
                    'users.middle_name',
                    'users.last_name',
                )
                ->get();

            return Inertia::render('dashboard/pre/(faculty)/Index', [
                'students' => $students_data,
            ]);
        }
        return Inertia::render('dashboard/' . $phase . '/(faculty)/Index');
    }

    public function showFacultyStudent(int $student_number): Response
    {
        $student = DB::table('students')
            ->where('student_number', $student_number)
            ->join('users', 'students.student_number', '=', 'users.role_id')
            ->where('users.role', 'student')
            ->select(
                'students.student_number',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
            )
            ->firstOrFail();

        $submission_statuses = DB::table('submission_statuses')
            ->where('student_number', $student_number)
            ->join('requirements', 'submission_statuses.requirement_id', '=', 'requirements.id')
            ->select(
                'requirements.id AS requirement_id',
                'requirements.requirement_name',
                'submission_statuses.status',
            )
            ->get();

        return Inertia::render('dashboard/pre/(faculty)/students/Index', [
            'student' => $student,
            'submissions' => $submission_statuses,
        ]);
    }

    public function submitStudentDocument(Request $request): RedirectResponse
    {
        $form_values = $request->validate([
            'requirementId' => ['required'],
            'file' => ['required', 'mimes:pdf', 'max:2048'],
        ]);

        $requirement_id = (int) $form_values['requirementId'];
        $student_number = Auth::user()->role_id;
        $submission_status = SubmissionStatus::where('student_number', $student_number)
            ->where('requirement_id', $requirement_id)
            ->firstOrFail();
        $submission_status->status = 'submitted';
        $submission_status->save();

        $submission = new Submission();
        $submission->submission_status_id = $submission_status->id;

        // TODO: Add proper revision and submission numbering
        $submission->revision_number = 1;
        $submission->submission_number = 1;

        $filepath = $request->file('file')->store('student/documents');
        $submission->filepath = $filepath;

        $submission->save();

        return redirect('/dashboard');
    }
}
