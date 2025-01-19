<?php

namespace App\Http\Controllers;

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

        $sections = DB::table('faculties')
            ->pluck('section');

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

        $requirement_names = DB::table('requirements')
            ->pluck('requirement_name');

        return Inertia::render('dashboard/(faculty)/students/Index', [
            'students' => $students,
            'requirementNames' => $requirement_names,
            'sections' => $sections
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
            $submission_status->status = 'pending';
        }

        $submission_status->save();

        return back();
    }

    public function assignStudentSection(int $student_number, string $new_section = '') {
        $faculty_sections = DB::table('faculties')
            ->pluck('id', 'section')
            ->toArray();

        if ($new_section === '') {
            // If section is set to nothing, set section to null
            $target_student = Student::find($student_number);
            $target_student->faculty_id = null;
            $target_student->has_dropped = false;
            $target_student->save();
        } else if ($new_section === 'DRP') {
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

        $supervisors_data = $users_partial
            ->select(
                'role_id AS supervisor_id',
                'first_name',
                'middle_name',
                'last_name',
            )
            ->get();

        return Inertia::render('dashboard/(faculty)/supervisors/Index', [
            'supervisors' => $supervisors_data,
        ]);
    }

    public function showSupervisor(int $supervisor_id): Response
    {
        $supervisor = DB::table('users')
            ->where('role', 'supervisor')
            ->where('role_id', $supervisor_id)
            ->select(
                'first_name',
                'middle_name',
                'last_name',
            )
            ->firstOrFail();

        $company_name = DB::table('supervisors')
            ->where('supervisors.id', $supervisor_id)
            ->join('companies', 'supervisors.company_id', '=', 'companies.id')
            ->select('companies.company_name')
            ->firstOrFail()
            ->company_name;

        $report_status = DB::table('report_statuses')
            ->where('supervisor_id', $supervisor_id)
            ->select('status')
            ->firstOrFail()
            ->status;

        /*
        * This might have issues if for some reason two interns under the
        * same supervisor do not have the same status, which should never happen.
        */
        $intern_evaluation_status = DB::table('intern_evaluation_statuses')
            ->where('supervisor_id', $supervisor_id)
            ->select('status')
            ->firstOrFail()
            ->status;

        return Inertia::render('dashboard/(faculty)/supervisors/[supervisor_id]/Index', [
            'supervisor_id' => $supervisor_id,
            'supervisor' => $supervisor,
            'company_name' => $company_name,
            'report_status' => $report_status,
            'intern_evaluation_status' => $intern_evaluation_status,
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

        return Inertia::render('dashboard/(faculty)/supervisors/[supervisor_id]/report/Index', [
            'supervisor_id' => $supervisor_id,
            'students' => $students,
        ]);
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

        return Inertia::render('dashboard/(faculty)/supervisors/[supervisor_id]/final/Index', [
            'supervisor_id' => $supervisor_id,
            'students' => $students,
        ]);
    }
}
