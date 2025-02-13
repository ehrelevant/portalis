<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Faculty;
use App\Models\Form;
use App\Models\FormStatus;
use App\Models\Requirement;
use App\Models\Student;
use App\Models\SubmissionStatus;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
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

        $students = [];

        $students_info = $users_partial
            ->join('students', 'users.role_id', '=', 'students.student_number')
            ->leftJoin('supervisors', 'supervisors.id', '=', 'students.supervisor_id')
            ->leftJoin('companies', 'companies.id', '=', 'supervisors.company_id')
            ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')
            ->select(
                'users.id AS user_id',
                'students.student_number',
                'users.first_name',
                'users.last_name',
                'faculties.section',
                'students.has_dropped',
                'companies.company_name'
            )
            ->orderBy('students.student_number')
            ->get();

        foreach ($students_info as $student_info) {
            $form_statuses = DB::table('form_statuses')
                ->join('forms', 'forms.id', '=', 'form_statuses.form_id')
                ->where('user_id', $student_info->user_id)
                ->pluck('status', 'form_id');

            $student_statuses = DB::table('submission_statuses')
                ->where('student_number', $student_info->student_number)
                ->select(
                    'submission_statuses.requirement_id',
                    'submission_statuses.status',
                )->get();

            array_push($students, [
                'student_number' => $student_info->student_number,
                'first_name' => $student_info->first_name,
                'last_name' => $student_info->last_name,
                'section' => $student_info->section,
                'company' => $student_info->company_name ?? '',
                'form_statuses' => $form_statuses,
                'has_dropped' => $student_info->has_dropped,
                'submissions' => $student_statuses,
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
            ->select('forms.id', 'forms.form_name', 'forms.short_name')
            ->groupBy('forms.id', 'forms.form_name', 'forms.short_name')
            ->get()
            ->keyBy('id');

        $companies = DB::table('companies')->get();
        $company_supervisors = [];
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

        return Inertia::render('dashboard/(admin)/students/StudentsList', [
            'students' => $students,
            'requirements' => $requirements,
            'sections' => $sections,
            'form_infos' => $form_infos,
            'companies' => $companies,
            'companySupervisors' => $company_supervisors,
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
            ->select('forms.id', 'forms.form_name', 'forms.short_name')
            ->groupBy('forms.id', 'forms.form_name', 'forms.short_name')
            ->get()
            ->keyBy('id');

        return Inertia::render('dashboard/(admin)/supervisors/SupervisorsList', [
            'supervisors' => $supervisors,
            'form_infos' => $form_infos,
        ]);
    }

    public function showFaculties(Request $request): Response
    {
        $search_text = $request->query('search') ?? '';

        $users_partial = DB::table('users')
            ->where('role', 'faculty')
            ->where(function ($query) use ($search_text) {
                $query->where('first_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search_text . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . $search_text . '%');
            });

        $faculties = $users_partial
            ->join('faculties', 'users.role_id', '=', 'faculties.id')
            ->select(
                'users.id AS user_id',
                'faculties.id AS faculty_id',
                'users.first_name',
                'users.last_name',
                'faculties.section',
            )
            ->orderBy('users.last_name')
            ->orderBy('users.first_name')
            ->get();

        return Inertia::render('dashboard/(admin)/faculties/FacultiesList', [
            'faculties' => $faculties,
        ]);
    }

    public function showCompanies(Request $request): Response
    {
        $search_text = $request->query('search') ?? '';

        $companies_partial = DB::table('companies')
            ->where(function ($query) use ($search_text) {
                $query->where('company_name', 'LIKE', '%' . $search_text . '%');
            });

        $companies = $companies_partial
            ->select(
                'companies.id AS company_id',
                'companies.company_name'
            )
            ->orderBy('companies.company_name')
            ->get();

        return Inertia::render('dashboard/(admin)/companies/CompaniesList', [
            'companies' => $companies,
        ]);
    }

    public function addStudent(Request $request)
    {
        $values = $request->validate([
            'student_number' => ['required', 'numeric', 'integer'],
            'first_name' => ['required', 'string'],
            'middle_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email:rfc'],
            'section' => ['nullable', 'string'],
            'supervisor_id' => ['nullable', 'numeric', 'integer'],
            'wordpress_name' => ['required', 'string'],
            'wordpress_email' => ['required', 'email:rfc'],
        ]);

        $new_student = new Student();
        $new_student->student_number = $values['student_number'];
        $new_student->supervisor_id = $values['supervisor_id'];
        if ($values['section']) {
            $new_student->faculty_id = DB::table('faculties')
                ->where('section', $values['section'])
                ->firstOrFail()
                ->id;
        } else {
            $new_student->faculty_id = null;
        }
        $new_student->wordpress_name = $values['wordpress_name'];
        $new_student->wordpress_email = $values['wordpress_email'];
        $new_student->save();

        $new_user = new User();
        $new_user->role = User::ROLE_STUDENT;
        $new_user->role_id = $new_student->student_number;
        $new_user->first_name = $values['first_name'];
        $new_user->middle_name = $values['middle_name'];
        $new_user->last_name = $values['last_name'];
        $new_user->email = $values['email'];
        $new_user->save();

        $requirements = DB::table('requirements')->get();
        foreach ($requirements as $requirement) {
            $new_submission_status = new SubmissionStatus();
            $new_submission_status->student_number = $new_student->student_number;
            $new_submission_status->requirement_id = $requirement->id;
            $new_submission_status->status = 'unsubmitted';
            $new_submission_status->save();
        }

        $forms = DB::table('forms')
            ->where('short_name', 'company-evaluation')
            ->orWhere('short_name', 'self-evaluation')
            ->get();
        foreach ($forms as $form) {
            $new_form_status = new FormStatus();
            $new_form_status->user_id = $new_user->id;
            $new_form_status->form_id = $form->id;
            $new_form_status->status = 'unsubmitted';
            $new_form_status->save();
        }

        return back();
    }

    public function addSupervisor(Request $request)
    {
        $values = $request->validate([
            'first_name' => ['required', 'string'],
            'middle_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email:rfc'],
        ]);

        $new_supervisor = new Supervisor();
        $new_supervisor->company_id = 1; // !PLACEHOLDER
        $new_supervisor->save();

        $new_user = new User();
        $new_user->role = User::ROLE_SUPERVISOR;
        $new_user->role_id = $new_supervisor->id;
        $new_user->first_name = $values['first_name'];
        $new_user->middle_name = $values['middle_name'];
        $new_user->last_name = $values['last_name'];
        $new_user->email = $values['email'];
        $new_user->save();

        $forms = DB::table('forms')
            ->where('short_name', 'midsem')
            ->orWhere('short_name', 'final')
            ->orWhere('short_name', 'intern-evaluation')
            ->get();
        foreach ($forms as $form) {
            $new_form_status = new FormStatus();
            $new_form_status->user_id = $new_user->id;
            $new_form_status->form_id = $form->id;
            $new_form_status->status = 'unsubmitted';
            $new_form_status->save();
        }

        return back();
    }

    public function addFaculty(Request $request)
    {
        $values = $request->validate([
            'first_name' => ['required', 'string'],
            'middle_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email:rfc'],
            'section' => ['required', 'string'],
        ]);

        $new_faculty = new Faculty();
        $new_faculty->section = $values['section'];
        $new_faculty->save();

        $new_user = new User();
        $new_user->role = User::ROLE_FACULTY;
        $new_user->role_id = $new_faculty->id;
        $new_user->first_name = $values['first_name'];
        $new_user->middle_name = $values['middle_name'];
        $new_user->last_name = $values['last_name'];
        $new_user->email = $values['email'];
        $new_user->save();

        return back();
    }

    public function addCompany(Request $request)
    {
        $values = $request->validate([
            'company_name' => ['required', 'string'],
        ]);

        $new_company = new Company();
        $new_company->company_name = $values['company_name'];
        $new_company->save();

        return back();
    }

    public function deleteStudent(int $student_number)
    {
        User::where('role', User::ROLE_STUDENT)
            ->where('role_id', $student_number)
            ->firstOrFail()
            ->delete();

        Student::find($student_number)->delete();
    }

    public function deleteSupervisor(int $supervisor_id)
    {
        User::where('role', User::ROLE_SUPERVISOR)
            ->where('role_id', $supervisor_id)
            ->firstOrFail()
            ->delete();

        Supervisor::find($supervisor_id)->delete();
    }

    public function deleteFaculty(int $faculty_id)
    {
        Student::where('faculty_id', $faculty_id)
            ->update(['faculty_id' => null]);

        User::where('role', User::ROLE_FACULTY)
            ->where('role_id', $faculty_id)
            ->firstOrFail()
            ->delete();

        Faculty::find($faculty_id)->delete();
    }

    public function deleteCompany(int $company_id)
    {
        Supervisor::where('company_id', $company_id)
            ->update(['company_id' => null]);

        Company::find($company_id)->delete();
    }
}
