<?php

namespace App\Http\Controllers;

use App\Models\InternEvaluation;
use App\Models\InternEvaluationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class FormController extends Controller
{
    public function showMidsemReport(int $supervisor_id)
    {
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

            $new_student = [
                'student_number' => $student_number,
                'last_name' => $student_name->last_name,
                'first_name' => $student_name->first_name,
                'ratings' => $ratings,
                'open_ended' => $open_ended,
                'hours' => $hours,
            ];

            array_push($students, $new_student);
        }

        $status = DB::table('report_statuses')
            ->where('supervisor_id', $supervisor_id)
            ->select('status')
            ->firstOrFail()
            ->status;

        return Inertia::render('dashboard/(faculty)/supervisors/midsem/Index', [
            'supervisor_id' => $supervisor_id,
            'students' => $students,
            'status' => $status,
        ]);
    }

    public function validateMidsemReport(int $supervisor_id): RedirectResponse
    {
        $report_status = ReportStatus::where('supervisor_id', $supervisor_id)
            ->firstOrFail();

        if ($report_status->status === 'submitted') {
            ReportStatus::where('supervisor_id', $supervisor_id)
                ->update(['status' => 'validated']);
        }

        return back();
    }

    public function invalidateMidsemReport(int $supervisor_id): RedirectResponse
    {
        $report_statuses = ReportStatus::where('supervisor_id', $supervisor_id)
            ->firstOrFail();

        if ($report_statuses->status === 'validated') {
            ReportStatus::where('supervisor_id', $supervisor_id)
                ->update(['status' => 'submitted']);
        }

        return back();
    }

    public function rejectMidsemReport(int $supervisor_id): RedirectResponse
    {
        $report_status = ReportStatus::where('supervisor_id', $supervisor_id)
            ->firstOrFail();

        if ($report_status->status === 'submitted') {
            ReportStatus::where('supervisor_id', $supervisor_id)
                ->update(['status' => 'rejected']);
        }

        return back();
    }

    public function showFinalReport(int $supervisor_id)
    {
        $intern_evaluations = DB::table('intern_evaluation_statuses')
            ->where('supervisor_id', $supervisor_id)
            ->join(
                'intern_evaluations',
                'intern_evaluations.intern_evaluation_status_id',
                '=',
                'intern_evaluation_statuses.id'
            )
            ->select('intern_evaluations.id', 'intern_evaluation_statuses.student_number')
            ->get();

        $students = [];

        foreach ($intern_evaluations as $intern_evaluation) {
            $report_id = $intern_evaluation->id;
            $student_number = $intern_evaluation->student_number;

            $student_name = DB::table('users')
                ->where('role', 'student')
                ->where('role_id', $student_number)
                ->select('first_name', 'last_name')
                ->firstOrFail();

            $ratings = DB::table('intern_evaluation_ratings')
                ->where('intern_evaluation_id', $report_id)
                ->pluck('score', 'rating_question_id')
                ->toArray();

            $open_ended = DB::table('intern_evaluation_answers')
                ->where('intern_evaluation_id', $report_id)
                ->pluck('answer', 'open_ended_question_id')
                ->toArray();

            $new_student = [
                'student_number' => $student_number,
                'last_name' => $student_name->last_name,
                'first_name' => $student_name->first_name,
                'ratings' => $ratings,
                'open_ended' => $open_ended,
            ];

            array_push($students, $new_student);
        }

        $status = DB::table('intern_evaluation_statuses')
            ->where('supervisor_id', $supervisor_id)
            ->select('status')
            ->firstOrFail()
            ->status;

        return Inertia::render('dashboard/(faculty)/supervisors/final/Index', [
            'supervisor_id' => $supervisor_id,
            'students' => $students,
            'status' => $status,
        ]);
    }

    public function validateFinalReport(int $supervisor_id): RedirectResponse
    {
        $report_status = InternEvaluationStatus::where('supervisor_id', $supervisor_id)
            ->firstOrFail();

        if ($report_status->status === 'submitted') {
            InternEvaluationStatus::where('supervisor_id', $supervisor_id)
                ->update(['status' => 'validated']);
        }

        return back();
    }

    public function invalidateFinalReport(int $supervisor_id): RedirectResponse
    {
        $report_status = InternEvaluationStatus::where('supervisor_id', $supervisor_id)
            ->firstOrFail();

        if ($report_status->status === 'validated') {
            InternEvaluationStatus::where('supervisor_id', $supervisor_id)
                ->update(['status' => 'submitted']);
        }

        return back();
    }

    public function rejectFinalReport(int $supervisor_id): RedirectResponse
    {
        $report_status = InternEvaluationStatus::where('supervisor_id', $supervisor_id)
            ->firstOrFail();

        if ($report_status->status === 'submitted') {
            InternEvaluationStatus::where('supervisor_id', $supervisor_id)
                ->update(['status' => 'rejected']);
        }

        return back();
    }
}
