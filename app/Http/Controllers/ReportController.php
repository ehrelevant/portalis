<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportAnswer;
use App\Models\ReportRating;
use App\Models\ReportStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function show()
    {
        $supervisor_user = Auth::user();

        $supervised_students = DB::table('students')
            ->where('supervisor_id', $supervisor_user->role_id)
            ->join('users', 'students.student_number', '=', 'users.role_id')
            ->where('role', 'student')
            ->select('students.student_number', 'users.first_name', 'users.last_name')
            ->get();

        return Inertia::render('dashboard/during/(supervisor)/report/Index', [
            'students' => $supervised_students,
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $form_values = $request->validate([
            'evaluations.*.student_number' => ['integer', 'numeric'],
            'evaluations.*.ratings' => ['required', 'array'],
            'evaluations.*.ratings.*' => ['required', 'integer', 'numeric'],
            'evaluations.*.hours' => ['required', 'integer', 'numeric'],
            'evaluations.*.open_ended' => ['array'],
            'evaluations.*.open_ended.*' => ['nullable', 'string'],
        ]);

        $supervisor_user = Auth::user();

        foreach ($form_values['evaluations'] as $evaluation) {
            $report_status = ReportStatus::where('supervisor_id', $supervisor_user->role_id)
                ->where('student_number', $evaluation['student_number'])
                ->firstOrFail();
            $report_status->status = 'submitted';
            $report_status->save();

            $new_report = new Report();
            $new_report->report_status_id = $report_status->id;
            $new_report->total_hours = $evaluation['hours'];
            $new_report->save();

            foreach ($evaluation['ratings'] as $i => $score) {
                $report_rating = new ReportRating();
                $report_rating->report_id = $new_report->id;
                $report_rating->rating_question_id = $i;
                $report_rating->score = $score;
                $report_rating->save();
            }

            foreach ($evaluation['open_ended'] as $i => $open_ended) {
                $report_open_ended = new ReportAnswer();
                $report_open_ended->report_id = $new_report->id;
                $report_open_ended->open_ended_question_id = $i;
                $report_open_ended->answer = $open_ended ?? '';
                $report_open_ended->save();
            }
        }

        return redirect('/dashboard');
    }
}
