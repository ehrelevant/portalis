<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class FacultyController extends Controller
{
    public function showStudents(Request $request): Response
    {
        $phase = DB::table('website_states')->firstOrFail()->phase;

        $search_text = $request->query('search') ?? '';

        // TODO: Add student number search
        $users_partial = DB::table('users')
            ->where('role', 'student')
            ->where(function ($query) use ($search_text) {
                $query->where('first_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . $search_text . '%');
            });

        $students = [];

        switch ($phase) {
            case 'pre':
                $students_info = $users_partial
                    ->join('students', 'users.role_id', '=', 'students.student_number')
                    ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')
                    ->select(
                        'users.id AS user_id',
                        'students.student_number',
                        'users.first_name',
                        'users.last_name',
                        'faculties.section',
                        'users.email',
                        'students.wordpress_name',
                        'students.wordpress_email',
                        'students.has_dropped',
                    )
                    ->orderBy('students.student_number')
                    ->get();

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
                        'email' => $student_info->email,
                        'wordpress_name' => $student_info->wordpress_name,
                        'wordpress_email' => $student_info->wordpress_email,
                        'has_dropped' => $student_info->has_dropped,
                        'submissions' => $student_statuses,
                    ];

                    array_push($students, $new_student);
                }

                $requirements = DB::table('requirements')
                    ->get();

                $sections = DB::table('faculties')
                    ->pluck('section');

                return Inertia::render('dashboard/(faculty)/students/RequirementsList', [
                    'students' => $students,
                    'requirements' => $requirements,
                    'sections' => $sections,
                ]);
            case 'during':
            case 'post':
                $students_info = $users_partial
                    ->join('students', 'users.role_id', '=', 'students.student_number')
                    ->leftJoin('supervisors', 'supervisors.id', '=', 'students.supervisor_id')
                    ->leftJoin('companies', 'companies.id', '=', 'supervisors.company_id')
                    ->whereNotNull('section')
                    ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')
                    ->select(
                        'users.id AS user_id',
                        'students.student_number',
                        'users.first_name',
                        'users.last_name',
                        'faculties.section',
                        'companies.company_name',
                        'students.supervisor_id',
                        'users.email',
                        'students.wordpress_name',
                        'students.wordpress_email',
                    )
                    ->orderBy('students.student_number')
                    ->get();

                foreach ($students_info as $student_info) {
                    $form_statuses = DB::table('form_statuses')
                        ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
                        ->where('forms.phase', $phase)
                        ->where('user_id', $student_info->user_id)
                        ->pluck('status', 'form_id');

                    array_push($students, [
                        'student_number' => $student_info->student_number,
                        'first_name' => $student_info->first_name,
                        'last_name' => $student_info->last_name,
                        'section' => $student_info->section,
                        'company' => $student_info->company_name ?? '',
                        'form_statuses' => $form_statuses,
                        'supervisor_id' => $student_info->supervisor_id,
                        'email' => $student_info->email,
                        'wordpress_name' => $student_info->wordpress_name,
                        'wordpress_email' => $student_info->wordpress_email,
                    ]);
                }

                $form_infos = DB::table('users')
                    ->where('role', 'student')
                    ->join('form_statuses', 'form_statuses.user_id', '=', 'users.id')
                    ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
                    ->where('forms.phase', $phase)
                    ->select('forms.id', 'forms.form_name', 'forms.short_name')
                    ->groupBy('forms.id', 'forms.form_name', 'forms.short_name')
                    ->get()
                    ->keyBy('id');

                $supervisors = DB::table('users')
                    ->where('role', 'supervisor')
                    ->join('supervisors', 'supervisors.id', '=', 'users.role_id')
                    ->select(
                        'supervisors.id',
                        'users.first_name',
                        'users.last_name',
                    )
                    ->get()
                    ->keyBy('id')
                    ->toArray();

                return Inertia::render('dashboard/(faculty)/students/FormsList', [
                    'students' => $students,
                    'form_infos' => $form_infos,
                    'supervisors' => $supervisors,
                ]);
        }
    }

    public function assignStudentSection(int $student_number, ?string $new_section = null)
    {
        $faculty_sections = DB::table('faculties')
            ->pluck('id', 'section')
            ->toArray();

        if (!$new_section) {
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
        }

        return back();
    }

    public function assignSupervisorCompany(int $supervisor_id, ?int $company_id = null)
    {
        $supervisor = Supervisor::find($supervisor_id);
        $supervisor->company_id = $company_id;
        $supervisor->save();

        return back();
    }

    public function assignStudentSupervisor(int $student_id, ?int $supervisor_id = null)
    {
        $student = Student::find($student_id);
        $student->supervisor_id = $supervisor_id;
        $student->save();

        return back();
    }

    public function showSupervisors(Request $request): Response
    {
        $phase = DB::table('website_states')->firstOrFail()->phase;

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
            ->leftJoin('companies', 'supervisors.company_id', '=', 'companies.id')
            ->select(
                'users.id AS user_id',
                'supervisors.id AS supervisor_id',
                'users.first_name',
                'users.last_name',
                'companies.company_name',
            )
            ->orderBy('users.last_name')
            ->orderBy('users.first_name')
            ->get();

        $supervisors = [];
        foreach ($supervisors_info as $supervisor_info) {
            $form_statuses = DB::table('form_statuses')
                ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
                ->where('forms.phase', $phase)
                ->where('user_id', $supervisor_info->user_id)
                ->pluck('status', 'form_id');

            array_push($supervisors, [
                'supervisor_id' => $supervisor_info->supervisor_id,
                'first_name' => $supervisor_info->first_name,
                'last_name' => $supervisor_info->last_name,
                'company_name' => $supervisor_info->company_name ?? '',
                'form_statuses' => $form_statuses,
            ]);
        }

        $form_infos = DB::table('users')
            ->where('role', 'supervisor')
            ->join('form_statuses', 'form_statuses.user_id', '=', 'users.id')
            ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
            ->where('forms.phase', $phase)
            ->select('forms.id', 'forms.form_name', 'forms.short_name')
            ->groupBy('forms.id', 'forms.form_name', 'forms.short_name')
            ->get()
            ->keyBy('id');

        return Inertia::render('dashboard/(faculty)/supervisors/FormsList', [
            'supervisors' => $supervisors,
            'form_infos' => $form_infos,
        ]);
    }
}
