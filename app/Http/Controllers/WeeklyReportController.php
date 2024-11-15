<?php

namespace App\Http\Controllers;

use App\Models\WeeklyReport;
use App\Models\WeeklyReportAnswer;
use App\Models\WeeklyReportRating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WeeklyReportController extends Controller
{
    public function show(int $week) {
        $supervisor_user = Auth::user();

        $supervised_students = DB::table('students')
            ->where('supervisor_id', $supervisor_user->role_id)
            ->join('users', 'students.student_number', '=', 'users.role_id')
            ->where('role', 'student')
            ->select('students.student_number', 'users.first_name', 'users.last_name')
            ->get();

        return Inertia::render('dashboard/during/(supervisor)/report/Index', [
            'students' => $supervised_students,
            'week' => $week,
        ]);
    }

    public function submitWeeklyReport(Request $request): RedirectResponse
    {
        $form_values = $request->validate([
            'week' => ['integer', 'numeric'],
            'evaluations.*.student_number' => ['integer', 'numeric'],
            'evaluations.*.ratings' => ['required', 'array'],
            'evaluations.*.ratings.*' => ['required', 'integer', 'numeric'],
            'evaluations.*.hours' => ['required', 'integer', 'numeric'],
            'evaluations.*.comments' => ['string'],
        ]);

        $supervisor_user = Auth::user();

        foreach ($form_values['evaluations'] as $evaluation) {
            $new_report = new WeeklyReport();
            $new_report->supervisor_id = $supervisor_user->role_id;
            $new_report->student_number = $evaluation['student_number'];
            $new_report->week = $form_values['week'];
            $new_report->total_hours = $evaluation['hours'];
            $new_report->save();

            foreach ($evaluation['ratings'] as $i => $score) {
                $report_rating = new WeeklyReportRating();
                $report_rating->weekly_report_id = $new_report->id;
                $report_rating->rating_question_id = $i;
                $report_rating->score = $score;
                $report_rating->save();
            }

            $report_comments = new WeeklyReportAnswer();
            $report_comments->weekly_report_id = $new_report->id;
            $report_comments->open_ended_question_id = 1;
            $report_comments->answer = $evaluation['comments'];
            $report_comments->save();
        }

        return redirect('/dashboard');
    }
}
