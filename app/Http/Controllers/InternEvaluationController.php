<?php

namespace App\Http\Controllers;

use App\Models\InternEvaluation;
use App\Models\InternEvaluationAnswer;
use App\Models\InternEvaluationRating;
use App\Models\InternEvaluationStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class InternEvaluationController extends Controller
{
    public function show(): Response
    {
        $supervisor_user = Auth::user();

        $supervised_students = DB::table('students')
            ->where('supervisor_id', $supervisor_user->role_id)
            ->join('users', 'students.student_number', '=', 'users.role_id')
            ->where('role', 'student')
            ->select('students.student_number', 'users.first_name', 'users.last_name')
            ->get();

        return Inertia::render('dashboard/during/(supervisor)/final/Index', [
            'students' => $supervised_students,
        ]);
    }

    public function submit(Request $request): RedirectResponse
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
