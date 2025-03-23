<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
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
        $sort_query = $request->query('sort') ?? 'student_number';
        $is_ascending_query = filter_var($request->query('ascending') ?? true, FILTER_VALIDATE_BOOLEAN);

        // TODO: Add student number search
        $users_partial = DB::table('users')
            ->where('users.role', 'student')
            ->where(function ($query) use ($search_text) {
                $query->where('users.first_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('users.last_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('users.middle_name', 'LIKE', '%' . $search_text . '%');
            });

        $students = [];

        switch ($phase) {
            case 'pre':
                $students_info = $users_partial
                    ->join('students', 'users.role_id', '=', 'students.id')
                    ->leftJoin('supervisors', 'supervisors.id', '=', 'students.supervisor_id')
                    ->leftJoin('users AS supervisor_users', 'supervisor_users.role_id', '=', 'supervisors.id')
                    ->where(function ($query) {
                        $query->where('supervisor_users.role', User::ROLE_SUPERVISOR)
                            ->orWhereNull('supervisor_users.id');
                    })
                    ->leftJoin('companies', 'companies.id', '=', 'supervisors.company_id')
                    ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')
                    ->select(
                        'users.id AS user_id',
                        'students.id AS student_id',
                        'students.student_number',
                        'users.first_name',
                        'users.middle_name',
                        'users.last_name',
                        'faculties.section',
                        'students.has_dropped',
                        'supervisors.id AS supervisor_id',
                        'supervisor_users.last_name AS supervisor_last_name',
                        'companies.id AS company_id',
                        'companies.company_name',
                        'users.email',
                        'students.wordpress_name',
                        'students.wordpress_email',
                        'users.is_disabled',
                    )
                    ->orderBy($sort_query, $is_ascending_query ? 'asc' : 'desc')
                    ->get();

                foreach ($students_info as $student_info) {
                    $student_statuses = DB::table('submission_statuses')
                        ->where('student_id', $student_info->student_id)
                        ->select(
                            'submission_statuses.requirement_id',
                            'submission_statuses.status',
                        )->get();

                    array_push($students, [
                        'student_id' => $student_info->student_id,
                        'student_number' => $student_info->student_number,
                        'first_name' => $student_info->first_name,
                        'middle_name' => $student_info->middle_name,
                        'last_name' => $student_info->last_name,
                        'section' => $student_info->section,
                        'supervisor_id' => $student_info->supervisor_id,
                        'company_id' => $student_info->company_id ?? 0,
                        'company' => $student_info->company_name ?? '',
                        'email' => $student_info->email,
                        'wordpress_name' => $student_info->wordpress_name,
                        'wordpress_email' => $student_info->wordpress_email,
                        'has_dropped' => $student_info->has_dropped,
                        'submissions' => $student_statuses,
                        'is_disabled' => $student_info->is_disabled,
                    ]);
                }

                $requirements = DB::table('requirements')
                    ->get();

                $sections = DB::table('faculties')
                    ->pluck('section');

                $companies = DB::table('companies')->get();
                $company_supervisors = [];

                $company_supervisors['0'] = DB::table('users')
                    ->where('role', 'supervisor')
                    ->join('supervisors', 'supervisors.id', '=', 'users.role_id')
                    ->whereNull('company_id')
                    ->select('supervisors.id', 'users.first_name', 'users.last_name')
                    ->get()
                    ->keyBy('id')
                    ->toArray();

                $supervisors = DB::table('users')
                    ->where('role', 'supervisor')
                    ->join('supervisors', 'supervisors.id', '=', 'users.role_id')
                    ->select('supervisors.id', 'users.first_name', 'users.last_name')
                    ->get()
                    ->keyBy('id')
                    ->toArray();

                foreach ($companies as $company) {
                    $company_supervisors_info = DB::table('users')
                        ->where('role', 'supervisor')
                        ->join('supervisors', 'supervisors.id', '=', 'users.role_id')
                        ->where('company_id', $company->id)
                        ->select('supervisors.id', 'users.first_name', 'users.last_name')
                        ->get()
                        ->keyBy('id')
                        ->toArray();

                    $company_supervisors[$company->id] = $company_supervisors_info;
                }

                return Inertia::render('dashboard/(faculty)/students/RequirementsList', [
                    'students' => $students,
                    'requirements' => $requirements,
                    'sections' => $sections,
                    'companies' => $companies,
                    'companySupervisors' => $company_supervisors,
                    'supervisors' => $supervisors,
                ]);
            case 'during':
            case 'post':
                $students_info = $users_partial
                    ->join('students', 'users.role_id', '=', 'students.id')
                    ->leftJoin('supervisors', 'supervisors.id', '=', 'students.supervisor_id')
                    ->leftJoin('users AS supervisor_users', 'supervisor_users.role_id', '=', 'supervisors.id')
                    ->where(function ($query) {
                        $query->where('supervisor_users.role', User::ROLE_SUPERVISOR)
                            ->orWhereNull('supervisor_users.id');
                    })
                    ->leftJoin('companies', 'companies.id', '=', 'supervisors.company_id')
                    ->whereNotNull('section')
                    ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')
                    ->select(
                        'users.id AS user_id',
                        'students.id AS student_id',
                        'students.student_number',
                        'users.first_name',
                        'users.last_name',
                        'faculties.section',
                        'students.has_dropped',
                        'supervisors.id AS supervisor_id',
                        'supervisor_users.last_name AS supervisor_last_name',
                        'companies.id AS company_id',
                        'companies.company_name',
                        'users.email',
                        'students.wordpress_name',
                        'students.wordpress_email',
                        'users.is_disabled',
                    )
                    ->orderBy($sort_query, $is_ascending_query ? 'asc' : 'desc')
                    ->get();

                foreach ($students_info as $student_info) {
                    $form_statuses = DB::table('form_statuses')
                        ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
                        ->where('forms.phase', $phase)
                        ->where('user_id', $student_info->user_id)
                        ->pluck('status', 'form_id');

                    $student_statuses = DB::table('submission_statuses')
                        ->where('student_id', $student_info->student_id)
                        ->select(
                            'submission_statuses.requirement_id',
                            'submission_statuses.status',
                        )->get();

                    array_push($students, [
                        'student_id' => $student_info->student_id,
                        'student_number' => $student_info->student_number,
                        'first_name' => $student_info->first_name,
                        'last_name' => $student_info->last_name,
                        'section' => $student_info->section,
                        'supervisor_id' => $student_info->supervisor_id,
                        'company_id' => $student_info->company_id,
                        'company' => $student_info->company_name,
                        'email' => $student_info->email,
                        'wordpress_name' => $student_info->wordpress_name,
                        'wordpress_email' => $student_info->wordpress_email,
                        'form_statuses' => $form_statuses,
                        'has_dropped' => $student_info->has_dropped,
                        'submissions' => $student_statuses,
                        'is_disabled' => $student_info->is_disabled,
                    ]);
                }

                $requirements = DB::table('requirements')
                    ->get();

                $sections = DB::table('faculties')
                    ->pluck('section');

                $form_infos = DB::table('users')
                    ->where('role', 'student')
                    ->join('form_statuses', 'form_statuses.user_id', '=', 'users.id')
                    ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
                    ->where('forms.phase', $phase)
                    ->select('forms.id', 'forms.form_name', 'forms.short_name')
                    ->groupBy('forms.id', 'forms.form_name', 'forms.short_name')
                    ->get()
                    ->keyBy('id');

                $companies = DB::table('companies')->get();
                $company_supervisors = [];

                $company_supervisors['0'] = DB::table('users')
                    ->where('role', 'supervisor')
                    ->join('supervisors', 'supervisors.id', '=', 'users.role_id')
                    ->whereNull('company_id')
                    ->select('supervisors.id', 'users.first_name', 'users.last_name')
                    ->get()
                    ->keyBy('id')
                    ->toArray();

                $supervisors = DB::table('users')
                    ->where('role', 'supervisor')
                    ->join('supervisors', 'supervisors.id', '=', 'users.role_id')
                    ->select('supervisors.id', 'users.first_name', 'users.last_name')
                    ->get()
                    ->keyBy('id')
                    ->toArray();

                foreach ($companies as $company) {
                    $company_supervisors_info = DB::table('users')
                        ->where('role', 'supervisor')
                        ->join('supervisors', 'supervisors.id', '=', 'users.role_id')
                        ->where('company_id', $company->id)
                        ->select('supervisors.id', 'users.first_name', 'users.last_name')
                        ->get()
                        ->keyBy('id')
                        ->toArray();

                    $company_supervisors[$company->id] = $company_supervisors_info;
                }

                return Inertia::render('dashboard/(faculty)/students/FormsList', [
                    'students' => $students,
                    'sections' => $sections,
                    'form_infos' => $form_infos,
                    'companies' => $companies,
                    'companySupervisors' => $company_supervisors,
                    'supervisors' => $supervisors,
                ]);
        }
    }

    public function assignStudentSection(int $student_id, ?string $new_section = null)
    {
        $faculty_sections = DB::table('faculties')
            ->pluck('id', 'section')
            ->toArray();

        if (!$new_section) {
            // If section is set to nothing, set section to null
            $target_student = Student::find($student_id);
            $target_student->faculty_id = null;
            $target_student->has_dropped = false;
            $target_student->save();
        } elseif ($new_section === 'DRP') {
            // If section is set to DRP, flag student as DRP and set section to NULL in database
            $target_student = Student::find($student_id);
            $target_student->faculty_id = null;
            $target_student->has_dropped = true;
            $target_student->save();
        } else {
            $new_faculty_id = $faculty_sections[$new_section] ?? null;

            if ($new_faculty_id) {
                // If new section exists, save change in database
                $target_student = Student::find($student_id);
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
        $sort_query = $request->query('sort') ?? 'last_name';
        $is_ascending_query = filter_var($request->query('ascending'), FILTER_VALIDATE_BOOL, [FILTER_NULL_ON_FAILURE]) ?? true;

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
                'users.middle_name',
                'users.last_name',
                'users.email',
                'companies.id AS company_id',
                'companies.company_name',
                'users.is_disabled',
            )
            ->orderBy($sort_query, $is_ascending_query ? 'asc' : 'desc')
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
                'middle_name' => $supervisor_info->middle_name,
                'last_name' => $supervisor_info->last_name,
                'email' => $supervisor_info->email,
                'company_name' => $supervisor_info->company_name,
                'company_id' => $supervisor_info->company_id,
                'form_statuses' => $form_statuses,
                'is_disabled' => $supervisor_info->is_disabled,
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

        $companies = DB::table('companies')->get();

        return Inertia::render('dashboard/(faculty)/supervisors/SupervisorsList', [
            'supervisors' => $supervisors,
            'form_infos' => $form_infos,
            'companies' => $companies,
        ]);
    }

    public function showCompanies(Request $request): Response
    {
        $search_text = $request->query('search') ?? '';
        $sort_query = $request->query('sort') ?? 'company_name';
        $is_ascending_query = filter_var($request->query('ascending') ?? true, FILTER_VALIDATE_BOOLEAN);

        $companies_partial = DB::table('companies')
            ->where(function ($query) use ($search_text) {
                $query->where('company_name', 'LIKE', '%' . $search_text . '%');
            });

        $companies = $companies_partial
            ->select(
                'companies.id AS company_id',
                'companies.company_name',
                'companies.is_disabled'
            )
            ->orderBy($sort_query, $is_ascending_query ? 'asc' : 'desc')
            ->get();

        return Inertia::render('dashboard/(faculty)/companies/CompaniesList', [
            'companies' => $companies,
        ]);
    }
}
