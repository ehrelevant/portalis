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
use Inertia\Inertia;
use Inertia\Response;

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
                return Inertia::render('dashboard/' . $phase . '/(supervisor)/Index');
            case User::ROLE_FACULTY:
                // TODO: Move to a more dedicated function
                if ($phase == 'pre') {
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
                            DB::raw("MIN(submission_statuses.status) as total_status")
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
            case User::ROLE_ADMIN:
                return Inertia::render('dashboard/' . $phase . '/(admin)/Index');
        }

        abort(404);
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
        $submission->student_number = $student_number;
        $submission->requirement_id = $requirement_id;
        $submission->submission_date = now();

        // TODO: Add proper revision and submission numbering
        $submission->revision_number = 1;
        $submission->submission_number = 1;

        $filepath = $request->file('file')->store('student/documents');
        $submission->filepath = $filepath;

        $submission->save();

        return redirect('/dashboard');
    }
}
