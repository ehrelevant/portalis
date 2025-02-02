<?php

namespace App\Http\Controllers;

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

        $supervisors_info = $users_partial
            ->join('supervisors', 'users.role_id', '=', 'supervisors.id')
            ->join('companies', 'supervisors.company_id', '=', 'companies.id')
            ->select(
                'users.id AS user_id',
                'supervisors.id AS supervisor_id',
                'users.first_name',
                'users.last_name',
                'companies.company_name',
            )
            ->get();

        $supervisors = [];
        foreach ($supervisors_info as $supervisor_info) {
            $form_statuses = DB::table('form_statuses')
                ->where('user_id', $supervisor_info->user_id)
                ->pluck('status', 'form_id');

            array_push($supervisors, [
                'supervisor_id' => $supervisor_info->supervisor_id,
                'first_name' => $supervisor_info->first_name,
                'last_name' => $supervisor_info->last_name,
                'company_name' => $supervisor_info->company_name,
                'form_statuses' => $form_statuses,
            ]);
        }

        $form_infos = DB::table('users')
            ->where('role', 'supervisor')
            ->join('form_statuses', 'form_statuses.user_id', '=', 'users.id')
            ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
            ->select('forms.id', 'forms.form_name', 'forms.short_name')
            ->groupBy('forms.id', 'forms.form_name', 'forms.short_name')
            ->get()
            ->keyBy('id');

        return Inertia::render('dashboard/(faculty)/supervisors/Index', [
            'supervisors' => $supervisors,
            'form_infos' => $form_infos,
        ]);
    }
}
