<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FileSubmissionContoller extends Controller
{
    public function showStudentDocument(int $student_number, int $requirement_id): Response
    {
        $role = Auth::user()->role;
        $role_id = intval(Auth::user()->role_id);

        $submission_status_partial = DB::table('submission_statuses')
            ->where('student_number', $student_number)
            ->where('requirement_id', $requirement_id);

        $status = $submission_status_partial->firstOrFail()->status;

        // Students should only be able to see the most recent file if they have submitted/validated
        if (($role === 'student' && $role_id === $student_number && $status !== 'pending') || in_array($role, [User::ROLE_ADMIN, User::ROLE_FACULTY])) {
            $filepath = $submission_status_partial
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
}
