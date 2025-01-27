<?php

namespace App\Http\Controllers;

use App\Models\InternEvaluation;
use App\Models\InternEvaluationStatus;
use App\Models\ReportStatus;
use App\Models\Requirement;
use App\Models\Student;
use App\Models\SubmissionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class FacultyController extends Controller
{
    public function showStudents(Request $request): Response
    {
        $search_text = $request->query('search') ?? '';

        // TODO: Add student number search
        $users_partial = DB::table('users')
            ->where('role', 'student')
            ->where(function ($query) use ($search_text) {
                $query->where('first_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . $search_text . '%');
            });

        $students_info = $users_partial
            ->join('students', 'users.role_id', '=', 'students.student_number')
            ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')
            ->select(
                'students.student_number',
                'users.first_name',
                'users.last_name',
                'faculties.section',
                'students.has_dropped',
            )
            ->get();

        $students = [];

        foreach ($students_info as $student_info) {
            $student_statuses = DB::table('submission_statuses')
            ->where('student_number', $student_info->student_number)
            ->select(
                'submission_statuses.requirement_id',
                'submission_statuses.status',
            )->get();

            $new_student = [
                'student_number' => $student_info->student_number,
                'first_name' => $student_info->first_name,
                'last_name' => $student_info->last_name,
                'section' => $student_info->section,
                'has_dropped' => $student_info->has_dropped,
                'submissions' => $student_statuses,
            ];

            array_push($students, $new_student);
        }

        $requirements = DB::table('requirements')
            ->get();

        $sections = DB::table('faculties')
            ->pluck('section');

        return Inertia::render('dashboard/(faculty)/students/Index', [
            'students' => $students,
            'requirements' => $requirements,
            'sections' => $sections,
        ]);
    }

    public function showStudentSubmission(int $student_number, int $requirement_id)
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

    public function validateStudentSubmission(int $student_number, int $requirement_id): RedirectResponse
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

    public function invalidateStudentSubmission(int $student_number, int $requirement_id): RedirectResponse
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

    public function rejectStudentSubmission(int $student_number, int $requirement_id): RedirectResponse
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

    public function assignStudentSection(int $student_number, string $new_section = '')
    {
        $faculty_sections = DB::table('faculties')
            ->pluck('id', 'section')
            ->toArray();

        if ($new_section === '') {
            // If section is set to nothing, set section to null
            $target_student = Student::find($student_number);
            $target_student->faculty_id = null;
            $target_student->has_dropped = false;
            $target_student->save();
        } elseif ($new_section === 'DRP') {
            // If section is set to DRP, flag student as DRP and set section to NULL in database
            $target_student = Student::find($student_number);
            $target_student->faculty_id = null;
            $target_student->has_dropped = true;
            $target_student->save();
        } else {
            $new_faculty_id = $faculty_sections[$new_section] ?? null;

            if ($new_faculty_id) {
                // If new section exists, save change in database
                $target_student = Student::find($student_number);
                $target_student->faculty_id = $new_faculty_id;
                $target_student->has_dropped = false;
                $target_student->save();
            }
            // Otherwise, do not update database
            // This case only matters if an irregular section is passed
        }

        return back();
    }

    public function updateRequirementDeadlines(Request $request)
    {
        $form_values = $request->validate([
            'requirements.*.id' => ['int'],
            'requirements.*.deadline' => ['date', 'nullable'],
        ]);

        $new_requirements = $form_values['requirements'];

        foreach ($new_requirements as $new_requirement) {
            ['id' => $id, 'deadline' => $deadline] = $new_requirement;
            $requirement = Requirement::find($id);
            $requirement->deadline = $deadline;
            $requirement->save();
        }
    }

    public function showSupervisors(Request $request): Response
    {
        $search_text = $request->query('search') ?? '';

        $users_partial = DB::table('users')
            ->where('role', 'supervisor')
            ->where(function ($query) use ($search_text) {
                $query->where('first_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . $search_text . '%');
            });

        $supervisors = $users_partial
            ->join('supervisors', 'users.role_id', '=', 'supervisors.id')
            ->join('companies', 'supervisors.company_id', '=', 'companies.id')
            ->join('report_statuses', 'supervisors.id', '=', 'report_statuses.supervisor_id')
            ->join('intern_evaluation_statuses', 'supervisors.id', '=', 'intern_evaluation_statuses.supervisor_id')
            ->select(
                'supervisors.id AS supervisor_id',
                'users.first_name',
                'users.last_name',
                'companies.company_name',
                'report_statuses.status AS midsem_status',
                'intern_evaluation_statuses.status AS final_status'
            )
            ->groupBy(
                'supervisor_id',
                'first_name',
                'last_name',
                'company_name',
                'midsem_status',
                'final_status'
            )
            ->get();

        return Inertia::render('dashboard/(faculty)/supervisors/Index', [
            'supervisors' => $supervisors,
        ]);
    }

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
