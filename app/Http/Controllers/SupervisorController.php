<?php

namespace App\Http\Controllers;

use App\Models\InternEvaluation;
use App\Models\InternEvaluationAnswer;
use App\Models\InternEvaluationRating;
use App\Models\InternEvaluationStatus;
use App\Models\Report;
use App\Models\ReportAnswer;
use App\Models\ReportRating;
use App\Models\ReportStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SupervisorController extends Controller
{
    public function showMidsemReport(): Response
    {
        $supervisor_user = Auth::user();
        $supervisor_id = $supervisor_user->role_id;

        $reports = DB::table('report_statuses')
            ->where('supervisor_id', $supervisor_id)
            ->join(
                'reports',
                'reports.report_status_id',
                '=',
                'report_statuses.id'
            )
            ->select('reports.id', 'reports.total_hours', 'report_statuses.student_number')
            ->get();

        if ($reports->isEmpty()) {
            // Generate new report if it doesn't already exist
            $report_status_ids = DB::table('report_statuses')
                ->where('supervisor_id', $supervisor_user->role_id)
                ->pluck('id');

            // Hard-coded question ids for the "report" form
            // TODO: Move towards non-hardcoded forms
            $rating_question_ids = [1, 2, 3, 4, 5];
            $open_ended_question_ids = [1];

            foreach ($report_status_ids as $report_status_id) {
                $new_report = new Report();
                $new_report->report_status_id = $report_status_id;
                $new_report->save();

                foreach ($rating_question_ids as $rating_question_id) {
                    $report_rating = new ReportRating();
                    $report_rating->report_id = $new_report->id;
                    $report_rating->rating_question_id = $rating_question_id;
                    $report_rating->save();
                }

                foreach ($open_ended_question_ids as $open_ended_question_id) {
                    $report_answer = new ReportAnswer();
                    $report_answer->report_id = $new_report->id;
                    $report_answer->open_ended_question_id = $open_ended_question_id;
                    $report_answer->save();
                }
            }

            $reports = DB::table('report_statuses')
                ->where('supervisor_id', $supervisor_id)
                ->join(
                    'reports',
                    'reports.report_status_id',
                    '=',
                    'report_statuses.id'
                )
                ->select('reports.id', 'reports.total_hours', 'report_statuses.student_number')
                ->get();
        }

        $students = [];

        foreach ($reports as $report) {
            $report_id = $report->id;
            $hours = $report->total_hours;
            $student_number = $report->student_number;

            $student_name = DB::table('users')
                ->where('role', 'student')
                ->where('role_id', $student_number)
                ->select('first_name', 'last_name')
                ->firstOrFail();

            $ratings = DB::table('report_ratings')
                ->where('report_id', $report_id)
                ->pluck('score', 'rating_question_id')
                ->toArray();

            $open_ended = DB::table('report_answers')
                ->where('report_id', $report_id)
                ->pluck('answer', 'open_ended_question_id')
                ->toArray();

            $student = [
                'student_number' => $student_number,
                'last_name' => $student_name->last_name,
                'first_name' => $student_name->first_name,
                'ratings' => $ratings,
                'open_ended' => $open_ended,
                'hours' => $hours,
            ];

            array_push($students, $student);
        }

        return Inertia::render('dashboard/(supervisor)/midsem/Index', [
            'students' => $students,
        ]);
    }

    private function updateMidsemReport($report_values)
    {
        $supervisor_user = Auth::user();
        $supervisor_id = $supervisor_user->role_id;

        foreach ($report_values['evaluations'] as $evaluation) {
            $report_status_id = ReportStatus::where('supervisor_id', $supervisor_id)
                ->where('student_number', $evaluation['student_number'])
                ->firstOrFail()
                ->id;

            $report = Report::where('report_status_id', $report_status_id)
                ->firstOrFail();
            $report->total_hours = $evaluation['hours'];
            $report->save();

            foreach ($evaluation['ratings'] as $rating_question_id => $score) {
                $report_rating = ReportRating::where('report_id', $report->id)
                    ->where('rating_question_id', $rating_question_id)
                    ->firstOrFail();
                $report_rating->score = $score;
                $report_rating->save();
            }

            foreach ($evaluation['open_ended'] as $open_ended_question_id => $answer) {
                $report_open_ended = ReportAnswer::where('report_id', $report->id)
                    ->where('open_ended_question_id', $open_ended_question_id)
                    ->firstOrFail();
                $report_open_ended->answer = $answer;
                $report_open_ended->save();
            }
        }
    }

    public function draftMidsemReport(Request $request): RedirectResponse
    {
        $form_values = $request->validate([
            'evaluations.*.student_number' => ['integer', 'numeric'],
            'evaluations.*.ratings' => ['array'],
            'evaluations.*.ratings.*' => ['nullable'],
            'evaluations.*.open_ended' => ['array'],
            'evaluations.*.open_ended.*' => ['nullable'],
            'evaluations.*.hours' => ['nullable'],
        ]);

        $this->updateMidsemReport($form_values);

        return redirect('/dashboard');
    }

    public function submitMidsemReport(Request $request): RedirectResponse
    {
        $form_values = $request->validate([
            'evaluations.*.student_number' => ['integer', 'numeric'],
            'evaluations.*.ratings' => ['required', 'array'],
            'evaluations.*.ratings.*' => ['required', 'integer', 'numeric'],
            'evaluations.*.open_ended' => ['array'],
            'evaluations.*.open_ended.*' => ['nullable', 'string'],
            'evaluations.*.hours' => ['required', 'integer', 'numeric'],
        ]);

        $this->updateMidsemReport($form_values);

        return redirect('/dashboard');

        $supervisor_user = Auth::user();
        $supervisor_id = $supervisor_user->id;

        // Update status for all reports under the supervisor
        ReportStatus::where('supervisor_id', $supervisor_id)
            ->update(['status' => 'submitted']);

        return redirect('/dashboard');
    }

    public function showFinalReport(): Response
    {
        $supervisor_user = Auth::user();

        $supervised_students = DB::table('students')
            ->where('supervisor_id', $supervisor_user->role_id)
            ->join('users', 'students.student_number', '=', 'users.role_id')
            ->where('role', 'student')
            ->select('students.student_number', 'users.first_name', 'users.last_name')
            ->get();

        return Inertia::render('dashboard/(supervisor)/final/Index', [
            'students' => $supervised_students,
        ]);
    }

    public function submitFinalReport(Request $request): RedirectResponse
    {
        $form_values = $request->validate([
            'evaluations.*.student_number' => ['integer', 'numeric'],
            'evaluations.*.ratings' => ['required', 'array'],
            'evaluations.*.ratings.*' => ['required', 'integer', 'numeric'],
            'evaluations.*.open_ended' => ['array'],
            'evaluations.*.open_ended.*' => ['nullable', 'string'],
        ]);

        $supervisor_user = Auth::user();

        foreach ($form_values['evaluations'] as $evaluation) {
            $evaluation_status = InternEvaluationStatus::where('supervisor_id', $supervisor_user->role_id)
                ->where('student_number', $evaluation['student_number'])
                ->firstOrFail();
            $evaluation_status->status = 'submitted';
            $evaluation_status->save();

            $new_evaluation = new InternEvaluation();
            $new_evaluation->intern_evaluation_status_id = $evaluation_status->id;
            $new_evaluation->save();

            foreach ($evaluation['ratings'] as $i => $score) {
                $evaluation_ratings = new InternEvaluationRating();
                $evaluation_ratings->intern_evaluation_id = $new_evaluation->id;
                $evaluation_ratings->rating_question_id = $i;
                $evaluation_ratings->score = $score;
                $evaluation_ratings->save();
            }

            foreach ($evaluation['open_ended'] as $i => $open_ended) {
                $evaluation_open_ended = new InternEvaluationAnswer();
                $evaluation_open_ended->intern_evaluation_id = $new_evaluation->id;
                $evaluation_open_ended->open_ended_question_id = $i;
                $evaluation_open_ended->answer = $open_ended ?? '';
                $evaluation_open_ended->save();
            }
        }

        return redirect('/dashboard');
    }
}
