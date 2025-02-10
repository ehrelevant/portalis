<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ReportStatus;
use App\Models\Requirement;
use App\Models\Student;
use App\Models\SubmissionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileSubmissionContoller extends Controller
{
    public function showStudentDocument(int $requirement_id, int $student_number): StreamedResponse
    {
        $role = Auth::user()->role;
        $role_id = intval(Auth::user()->role_id);

        $status = DB::table('submission_statuses')
            ->where('student_number', $student_number)
            ->where('requirement_id', $requirement_id)
            ->firstOrFail()
            ->status;

        // Students should only be able to see the most recent file if they have submitted/validated
        if (($role === 'student' && $role_id === $student_number && $status !== 'pending') || in_array($role, [User::ROLE_ADMIN, User::ROLE_FACULTY])) {
            $filepath = DB::table('submission_statuses')
                ->where('student_number', $student_number)
                ->where('requirement_id', $requirement_id)
                ->join('submissions', 'submission_statuses.id', '=', 'submissions.submission_status_id')
                ->orderBy('created_at', 'desc')
                ->firstOrFail()
                ->filepath;

            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return Storage::response(
                $filepath,
                headers: $headers
            );
        }

        abort(401);
    }

    public function showStudentSubmission(int $requirement_id, int $student_number)
    {
        $submission_status = SubmissionStatus::where('student_number', $student_number)
            ->where('requirement_id', $requirement_id)
            ->firstOrFail()
            ->status;

        return Inertia::render('dashboard/(faculty)/students/submission/Index', [
            'student_number' => $student_number,
            'requirement_id' => $requirement_id,
            'status' => $submission_status,
        ]);
    }

    public function validateStudentSubmission(int $requirement_id, int $student_number): RedirectResponse
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

    public function invalidateStudentSubmission(int $requirement_id, int $student_number): RedirectResponse
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

    public function rejectStudentSubmission(int $requirement_id, int $student_number): RedirectResponse
    {
        $submission_status = SubmissionStatus::where('student_number', $student_number)
            ->where('requirement_id', $requirement_id)
            ->firstOrFail();

        if ($submission_status->status === 'submitted') {
            $submission_status->status = 'rejected';
        }

        $submission_status->save();

        return back();
    }
}
