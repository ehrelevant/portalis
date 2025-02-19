<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Submission;
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
    public function showUploadForm(int $requirement_id, ?int $student_number = null): Response
    {
        $user = Auth::user();
        if ($user->role === User::ROLE_STUDENT) {
            if ($student_number && $student_number !== $user->role_id) {
                abort(401);
            } elseif (!$student_number) {
                $student_number = $user->role_id;
            }
        } elseif ($user->role === User::ROLE_ADMIN) {
            if (!$student_number) {
                abort(404);
            }
        } else {
            abort(401);
        }

        $student_name = DB::table('users')
            ->where('role', 'student')
            ->where('role_id', $student_number)
            ->select('first_name', 'last_name')
            ->firstOrFail();

        // Fetch thw id, name, and due date of a requirement for displaying
        $requirement = DB::table('requirements')
            ->find($requirement_id);

        return Inertia::render('requirement/Upload', [
            'studentNumber' => $student_number,
            'studentName' => $student_name,
            'requirementId' => $requirement->id,
            'requirementName' => $requirement->requirement_name,
        ]);
    }

    public function submitDocument(Request $request, int $requirement_id, ?int $student_number = null): RedirectResponse
    {
        $user = Auth::user();
        if ($user->role === User::ROLE_STUDENT) {
            if ($student_number && $student_number != $user->role_id) {
                abort(401);
            } elseif (!$student_number) {
                $student_number = $user->role_id;
            }
        } elseif ($user->role === User::ROLE_ADMIN) {
            if (!$student_number) {
                abort(404);
            }
        } else {
            abort(401);
        }

        $request->validate([
            'file' => ['required', 'mimes:pdf', 'max:2048'],
        ]);

        $submission_status = SubmissionStatus::where('student_number', $student_number)
            ->where('requirement_id', $requirement_id)
            ->firstOrFail();
        $submission_status->status = 'For Review';
        $submission_status->save();

        $submission = new Submission();
        $submission->submission_status_id = $submission_status->id;

        // TODO: Add proper revision and submission numbering
        $submission->revision_number = 1;
        $submission->submission_number = 1;

        $filepath = $request->file('file')->store('student/documents');
        $submission->filepath = $filepath;

        $submission->save();

        if ($user->role === User::ROLE_ADMIN) {
            return redirect('/requirement/' . $requirement_id . '/view/' . $student_number);
        } else {
            return redirect('/dashboard');
        }
    }

    public function showStudentDocument(int $student_number, int $requirement_id): StreamedResponse
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

        $requirement_name = DB::table('requirements')
            ->where('id', $requirement_id)
            ->firstOrFail()
            ->requirement_name;

        $student_name = DB::table('users')
            ->where('role', 'student')
            ->where('role_id', $student_number)
            ->select('first_name', 'last_name')
            ->firstOrFail();

        return Inertia::render('requirement/View', [
            'studentNumber' => $student_number,
            'studentName' => $student_name,
            'requirementId' => $requirement_id,
            'requirementName' => $requirement_name,
            'status' => $submission_status,
            'isAdmin' => (Auth::user()->role === User::ROLE_ADMIN),
        ]);
    }

    public function validateStudentSubmission(int $requirement_id, int $student_number): RedirectResponse
    {
        $submission_status = SubmissionStatus::where('student_number', $student_number)
            ->where('requirement_id', $requirement_id)
            ->firstOrFail();

        if ($submission_status->status === 'For Review') {
            $submission_status->status = 'Accepted';
        }

        $submission_status->save();

        return back();
    }

    public function invalidateStudentSubmission(int $requirement_id, int $student_number): RedirectResponse
    {
        $submission_status = SubmissionStatus::where('student_number', $student_number)
            ->where('requirement_id', $requirement_id)
            ->firstOrFail();

        if ($submission_status->status === 'Accepted') {
            $submission_status->status = 'For Review';
        }

        $submission_status->save();

        return back();
    }

    public function rejectStudentSubmission(int $requirement_id, int $student_number): RedirectResponse
    {
        $submission_status = SubmissionStatus::where('student_number', $student_number)
            ->where('requirement_id', $requirement_id)
            ->firstOrFail();

        if ($submission_status->status === 'For Review') {
            $submission_status->status = 'Returned';
        }

        $submission_status->save();

        return back();
    }
}
