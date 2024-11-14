<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\SubmissionStatus;
use App\Models\User;
use App\Models\WebsiteState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function redirectPhase(): RedirectResponse
    {
        $phase = WebsiteState::findOrFail(1)->phase;
        return redirect('/dashboard/pre');
    }

    public function show(string $phase): Response
    {
        switch (Auth::user()->role) {
            case User::ROLE_STUDENT:
                return Inertia::render('dashboard/' . $phase . '/(student)/Index');
            case User::ROLE_SUPERVISOR:
                return Inertia::render('dashboard/' . $phase . '/(supervisor)/Index');
            case User::ROLE_FACULTY:
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
