<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\SubmissionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function showUploadDocument(Request $request, int $requirement_id): Response
    {
        // Fetch thw id, name, and due date of a requirement for displaying
        $requirement = DB::table('requirements')
            ->find($requirement_id);

        return Inertia::render('dashboard/(student)/upload/Index', [
            'requirementId' => $requirement->id,
            'requirementName' => $requirement->requirement_name,
        ]);
    }

    public function submitDocument(Request $request, int $requirement_id): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'mimes:pdf', 'max:2048'],
        ]);

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
