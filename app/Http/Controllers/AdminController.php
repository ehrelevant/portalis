<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Faculty;
use App\Models\FormAnswer;
use App\Models\FormStatus;
use App\Models\OpenAnswer;
use App\Models\RatingScore;
use App\Models\Student;
use App\Models\Submission;
use App\Models\SubmissionStatus;
use App\Models\Supervisor;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function showStudents(Request $request): Response
    {
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

        return Inertia::render('dashboard/(admin)/StudentsList', [
            'students' => $students,
            'requirements' => $requirements,
            'sections' => $sections,
            'form_infos' => $form_infos,
            'companies' => $companies,
            'companySupervisors' => $company_supervisors,
        ]);
    }

    public function showSupervisors(Request $request): Response
    {
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
                ->where('user_id', $supervisor_info->user_id)
                ->pluck('status', 'form_id');

            array_push($supervisors, [
                'supervisor_id' => $supervisor_info->supervisor_id,
                'first_name' => $supervisor_info->first_name,
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
            ->select('forms.id', 'forms.form_name', 'forms.short_name')
            ->groupBy('forms.id', 'forms.form_name', 'forms.short_name')
            ->get()
            ->keyBy('id');

        $companies = DB::table('companies')->get();

        return Inertia::render('dashboard/(admin)/SupervisorsList', [
            'supervisors' => $supervisors,
            'form_infos' => $form_infos,
            'companies' => $companies,
        ]);
    }

    public function showFaculties(Request $request): Response
    {
        $search_text = $request->query('search') ?? '';
        $sort_query = $request->query('sort') ?? 'last_name';
        $is_ascending_query = filter_var($request->query('ascending') ?? true, FILTER_VALIDATE_BOOLEAN);

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
                'users.email',
                'faculties.section',
                'users.is_disabled'
            )
            ->orderBy($sort_query, $is_ascending_query ? 'asc' : 'desc')
            ->get();

        return Inertia::render('dashboard/(admin)/FacultiesList', [
            'faculties' => $faculties,
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
                'companies.company_name'
            )
            ->orderBy($sort_query, $is_ascending_query ? 'asc' : 'desc')
            ->get();

        return Inertia::render('dashboard/(admin)/CompaniesList', [
            'companies' => $companies,
        ]);
    }

    public function addStudent(Request $request)
    {
        try {
            $values = $request->validate([
                'student_number' => ['required', 'string'],
                'first_name' => ['required', 'string'],
                'middle_name' => ['nullable', 'string'],
                'last_name' => ['required', 'string'],
                'email' => ['required', 'email:rfc'],
                'section' => ['nullable', 'string'],
                'supervisor_id' => ['nullable', 'numeric', 'integer'],
                'wordpress_name' => ['required', 'string'],
                'wordpress_email' => ['required', 'email:rfc'],
            ]);

            $student_number_exists = Student::where('student_number', $values['student_number'])->exists();
            $user_email_exists = User::where('email', $values['email'])->exists();

            if ($student_number_exists) {
                return back()->withErrors([
                    'student_number' => 'The provided student number already exists.',
                ])->with('error', 'Failed to add student.');
            } else if ($user_email_exists) {
                return back()->withErrors([
                    'email' => 'The provided email already exists.',
                ])->with('error', 'Failed to add student.');
            }

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
            $new_user->role_id = $new_student->id;
            $new_user->first_name = $values['first_name'];
            $new_user->middle_name = $values['middle_name'] ?? '';
            $new_user->last_name = $values['last_name'];
            $new_user->email = $values['email'];
            $new_user->save();

            $requirements = DB::table('requirements')->get();
            foreach ($requirements as $requirement) {
                $new_submission_status = new SubmissionStatus();
                $new_submission_status->student_id = $new_student->id;
                $new_submission_status->requirement_id = $requirement->id;
                $new_submission_status->status = 'None';
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
                $new_form_status->status = 'None';
                $new_form_status->save();
            }

            return back()->with('success', 'Successfully added student.');
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->withErrors([
                'unknown' => ''
            ])->with('error', 'Failed to add student.');
        }
    }

    public function addSupervisor(Request $request)
    {
        try {
            $values = $request->validate([
                'first_name' => ['required', 'string'],
                'middle_name' => ['nullable', 'string'],
                'last_name' => ['required', 'string'],
                'email' => ['required', 'email:rfc'],
                'company_id' => ['nullable', 'numeric', 'integer'],
            ]);

            $user_email_exists = User::where('email', $values['email'])->exists();

            if ($user_email_exists) {
                return back()->withErrors([
                    'email' => 'The provided email already exists.',
                ])->with('error', 'Failed to add supervisor.');
            }

            $new_supervisor = new Supervisor();
            $new_supervisor->company_id = $values['company_id'];
            $new_supervisor->save();

            $new_user = new User();
            $new_user->role = User::ROLE_SUPERVISOR;
            $new_user->role_id = $new_supervisor->id;
            $new_user->first_name = $values['first_name'];
            $new_user->middle_name = $values['middle_name'] ?? '';
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
                $new_form_status->status = 'None';
                $new_form_status->save();
            }

            return back()->with('success', 'Successfully added supervisor.');
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->withErrors([
                'unknown' => ''
            ])->with('error', 'Failed to add supervisor.');
        }
    }

    public function addFaculty(Request $request)
    {
        try {
            $values = $request->validate([
                'first_name' => ['required', 'string'],
                'middle_name' => ['nullable', 'string'],
                'last_name' => ['required', 'string'],
                'email' => ['required', 'email:rfc'],
                'section' => ['nullable', 'string'],
            ]);

            $user_email_exists = User::where('email', $values['email'])->exists();
            $section_exists = Faculty::where('section', $values['section'])->exists();

            if ($user_email_exists) {
                return back()->withErrors([
                    'email' => 'The provided email already exists.',
                ])->with('error', 'Failed to add faculty.');
            } else if ($values['section'] && $section_exists) {
                return back()->withErrors([
                    'section' => 'The provided section already exists.',
                ])->with('error', 'Failed to add faculty.');
            }

            $new_faculty = new Faculty();
            $new_faculty->section = $values['section'];
            $new_faculty->save();

            $new_user = new User();
            $new_user->role = User::ROLE_FACULTY;
            $new_user->role_id = $new_faculty->id;
            $new_user->first_name = $values['first_name'];
            $new_user->middle_name = $values['middle_name'] ?? '';
            $new_user->last_name = $values['last_name'];
            $new_user->email = $values['email'];
            $new_user->save();

            return back()->with('success', 'Successfully added faculty.');
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->withErrors([
                'unknown' => ''
            ])->with('error', 'Failed to add faculty.');
        }
    }

    public function addCompany(Request $request)
    {
        try {
            $values = $request->validate([
                'company_name' => ['required', 'string'],
            ]);

            $new_company = new Company();
            $new_company->company_name = $values['company_name'];
            $new_company->save();

            return back()->with('success', 'Successfully added company.');
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->withErrors([
                'unknown' => ''
            ])->with('error', 'Failed to add company.');
        }
    }

    public function updateStudent(Request $request, int $student_id)
    {
        $values = $request->validate([
            'student_number' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'middle_name' => ['nullable', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email:rfc'],
            'section' => ['nullable', 'string'],
            'supervisor_id' => ['nullable', 'numeric', 'integer'],
            'wordpress_name' => ['required', 'string'],
            'wordpress_email' => ['required', 'email:rfc'],
        ]);

        $student_number_exists = Student::where('student_number', $values['student_number'])
            ->whereNot('id', $student_id)
            ->exists();

        $user = User::where('role', User::ROLE_STUDENT)->where('role_id', $student_id)->firstOrFail();
        $user_email_exists = User::where('email', $values['email'])
            ->whereNot('id', $user->id)
            ->exists();

        if ($student_number_exists) {
            return back()->withErrors([
                'student_number' => 'The provided student number already exists.',
            ]);
        } else if ($user_email_exists) {
            return back()->withErrors([
                'email' => 'The provided email already exists.',
            ]);
        }

        $user->first_name = $values['first_name'];
        $user->middle_name = $values['middle_name'] ?? '';
        $user->last_name = $values['last_name'];
        $user->email = $values['email'];
        $user->save();

        $student = Student::find($student_id);
        $student->student_number = $values['student_number'];
        if ($values['section']) {
            $student->faculty_id = DB::table('faculties')
                ->where('section', $values['section'])
                ->firstOrFail()
                ->id;
        }
        $student->supervisor_id = $values['supervisor_id'];
        $student->wordpress_name = $values['wordpress_name'];
        $student->wordpress_email = $values['wordpress_email'];
        $student->save();

        return back();
    }

    public function updateSupervisor(Request $request, int $supervisor_id)
    {
        $values = $request->validate([
            'first_name' => ['required', 'string'],
            'middle_name' => ['nullable', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email:rfc'],
            'company_id' => ['nullable', 'numeric', 'integer'],
        ]);

        $user = User::where('role', User::ROLE_SUPERVISOR)->where('role_id', $supervisor_id)->firstOrFail();
        $user_email_exists = User::where('email', $values['email'])
            ->whereNot('id', $user->id)
            ->exists();

        if ($user_email_exists) {
            return back()->withErrors([
                'email' => 'The provided email already exists.',
            ]);
        }

        $user->first_name = $values['first_name'];
        $user->middle_name = $values['middle_name'] ?? '';
        $user->last_name = $values['last_name'];
        $user->email = $values['email'];
        $user->save();

        $supervisor = Supervisor::find($supervisor_id);
        $supervisor->company_id = $values['company_id'];
        $supervisor->save();

        return back();
    }

    public function updateFaculty(Request $request, int $faculty_id)
    {
        $values = $request->validate([
            'first_name' => ['required', 'string'],
            'middle_name' => ['nullable', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'email:rfc'],
            'section' => ['nullable', 'string'],
        ]);

        $user = User::where('role', User::ROLE_FACULTY)->where('role_id', $faculty_id)->firstOrFail();
        $user_email_exists = User::where('email', $values['email'])
            ->whereNot('id', $user->id)
            ->exists();

        $section_exists = Faculty::where('section', $values['section'])
            ->whereNot('id', $faculty_id)
            ->exists();

        if ($user_email_exists) {
            return back()->withErrors([
                'email' => 'The provided email already exists.',
            ]);
        } else if ($values['section'] && $section_exists) {
            return back()->withErrors([
                'section' => 'The provided section already exists.',
            ]);
        }

        $faculty = Faculty::find($faculty_id);
        $faculty->section = $values['section'];
        $faculty->save();

        $user->first_name = $values['first_name'];
        $user->middle_name = $values['middle_name'] ?? '';
        $user->last_name = $values['last_name'];
        $user->email = $values['email'];
        $user->save();

        return back();
    }

    public function updateCompany(Request $request, int $company_id)
    {
        $values = $request->validate([
            'company_name' => ['required', 'string'],
        ]);

        $new_company = Company::find($company_id);
        $new_company->company_name = $values['company_name'];
        $new_company->save();

        return back();
    }

    public function deleteStudent(int $student_id)
    {
        $user = User::where('role', User::ROLE_STUDENT)
            ->where('role_id', $student_id)
            ->firstOrFail();

        $submission_statuses = SubmissionStatus::where('student_id', $student_id)->get();
        foreach ($submission_statuses as $submission_status) {
            Submission::where('submission_status_id', $submission_status->id)->delete();
            $submission_status->delete();
        }

        $form_statuses = FormStatus::where('user_id', $user->id)->get();
        foreach ($form_statuses as $form_status) {
            $form_answers = FormAnswer::where('form_status_id', $form_status->id)->get();

            foreach ($form_answers as $form_answer) {
                RatingScore::where('form_answer_id', $form_answer->id)->delete();
                OpenAnswer::where('form_answer_id', $form_answer->id)->delete();

                $form_answer->delete();
            }
            $form_status->delete();
        }

        $user->delete();

        Student::find($student_id)->delete();
    }

    public function deleteSupervisor(int $supervisor_id)
    {
        $user = User::where('role', User::ROLE_SUPERVISOR)
            ->where('role_id', $supervisor_id)
            ->firstOrFail();

        FormAnswer::where('form_status_id', $user->id)->delete();
        $form_statuses = FormStatus::where('user_id', $user->id)->get();
        foreach ($form_statuses as $form_status) {
            $form_answers = FormAnswer::where('form_status_id', $form_status->id)->get();

            foreach ($form_answers as $form_answer) {
                RatingScore::where('form_answer_id', $form_answer->id)->delete();
                OpenAnswer::where('form_answer_id', $form_answer->id)->delete();

                $form_answer->delete();
            }
            $form_status->delete();
        }

        Student::where('supervisor_id', $supervisor_id)
            ->update(['supervisor_id' => null]);

        $user->delete();

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

    public function enableStudent(int $student_id)
    {
        User::where('role', User::ROLE_STUDENT)
            ->where('role_id', $student_id)
            ->update(['is_disabled' => false]);
    }

    public function enableSupervisor(int $supervisor_id)
    {
        User::where('role', User::ROLE_SUPERVISOR)
            ->where('role_id', $supervisor_id)
            ->update(['is_disabled' => false]);
    }

    public function enableFaculty(int $faculty_id)
    {
        User::where('role', User::ROLE_FACULTY)
            ->where('role_id', $faculty_id)
            ->update(['is_disabled' => false]);
    }

    public function disableStudent(int $student_id)
    {
        User::where('role', User::ROLE_STUDENT)
            ->where('role_id', $student_id)
            ->update(['is_disabled' => true]);
    }

    public function disableSupervisor(int $supervisor_id)
    {
        Student::where('supervisor_id', $supervisor_id)
            ->update(['supervisor_id' => null]);

        User::where('role', User::ROLE_SUPERVISOR)
            ->where('role_id', $supervisor_id)
            ->update(['is_disabled' => true]);
    }

    public function disableFaculty(int $faculty_id)
    {
        Student::where('faculty_id', $faculty_id)
            ->update(['faculty_id' => null]);

        User::where('role', User::ROLE_FACULTY)
            ->where('role_id', $faculty_id)
            ->update(['is_disabled' => true]);
    }
}
