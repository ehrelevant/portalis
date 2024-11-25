<?php

namespace App\Http\Controllers;

use App\Models\SubmissionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class FacultyController extends Controller
{
    public function showStudents(Request $request): Response
    {
        $search_text = $request->query('search') ?? '';

        // TODO: Add student number search
        $users_partial = DB::table('users')
            ->where('role', 'student')
            ->where(function ($query) use ($search_text) {
                $query->where('first_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . $search_text . '%');
            });

        $students_data = $users_partial
            ->join('students', 'users.role_id', '=', 'students.student_number')
            ->join('submission_statuses', 'students.student_number', '=', 'submission_statuses.student_number')
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

        return Inertia::render('dashboard/pre/(faculty)/students/Index', [
            'students' => $students_data,
        ]);
    }

    public function showStudent(int $student_number): Response
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

        return Inertia::render('dashboard/pre/(faculty)/students/[student_number]/Index', [
            'student' => $student,
            'submissions' => $submission_statuses,
        ]);
    }

    public function validateStudentSubmission(int $student_number, int $requirement_id): RedirectResponse
    {
        $submission_status = SubmissionStatus::where('student_number', $student_number)
            ->where('requirement_id', $requirement_id)
            ->firstOrFail();

        if ($submission_status->status === 'submitted') {
            $submission_status->status = 'validated';
        }

        $submission_status->save();

        return back();
    }

    public function invalidateStudentSubmission(int $student_number, int $requirement_id): RedirectResponse
    {
        $submission_status = SubmissionStatus::where('student_number', $student_number)
            ->where('requirement_id', $requirement_id)
            ->firstOrFail();

        if ($submission_status->status === 'validated') {
            $submission_status->status = 'submitted';
        }

        $submission_status->save();

        return back();
    }

    public function rejectStudentSubmission(int $student_number, int $requirement_id): RedirectResponse
    {
        $submission_status = SubmissionStatus::where('student_number', $student_number)
            ->where('requirement_id', $requirement_id)
            ->firstOrFail();

        if ($submission_status->status === 'submitted') {
            $submission_status->status = 'pending';
        }

        $submission_status->save();

        return back();
    }


    public function showSupervisors(Request $request): Response
    {
        $search_text = $request->query('search') ?? '';

        // TODO: Add student number search
        $users_partial = DB::table('users')
            ->where('role', 'supervisor')
            ->where(function ($query) use ($search_text) {
                $query->where('first_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . $search_text . '%');
            });

        $supervisors_data = $users_partial
            ->select(
                'role_id AS supervisor_id',
                'first_name',
                'middle_name',
                'last_name',
            )
            ->get();

        return Inertia::render('dashboard/pre/(faculty)/supervisors/Index', [
            'supervisors' => $supervisors_data,
        ]);
    }

    public function showSupervisor(int $supervisor_id): Response
    {
        $supervisor = DB::table('users')
            ->where('role', 'supervisor')
            ->where('role_id', $supervisor_id)
            ->select(
                'first_name',
                'middle_name',
                'last_name',
            )
            ->firstOrFail();

        return Inertia::render('dashboard/pre/(faculty)/supervisors/[supervisor_id]/Index', [
            'supervisor' => $supervisor,
        ]);
    }
}
