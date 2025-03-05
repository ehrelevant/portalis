<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ImportsController extends Controller
{
    public function showStudentCsvUploadForm(): Response
    {
        return Inertia::render('list/UploadStudents');
    }

    public function submitStudentCsv(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_FACULTY && $user->role !== User::ROLE_ADMIN) {
            abort(401);
        }

        // validate CSV MIME type
        // worst case, CSV has MIME type text/plain, which is basically the same as a regular .txt file
        // given a CSV file 1.csv, if it is renamed to 1.txt with no changes in data, then it would still be acceptable
        $request->validate([
            'file' => ['required', 'mimes:csv,txt'],
        ]);

        $filepath = $request->file('file')->store('list/students');
        self::deleteAllStudents();
        self::importStudents($filepath);

        // todo: add confirmation? view csv before proceeding with upload?
        return redirect('/dashboard/admin/students');
    }

    // todo: possibly move to AdminController?
    public function deleteAllStudents(): void
    {
        $student_ids = DB::table('users')
            ->where('role', 'student')
            ->pluck('role_id');
        
        $admin_controller = new AdminController();
        foreach ($student_ids as $student_id) {
            $admin_controller->deleteStudent($student_id);
        }
        
    }

    public function importStudents($csvPath): void
    {   
        // todo: clean up CSV importing (esp for non-local) (this path is not very good)
        $studentsCsv = fopen('../storage/app/private/' . $csvPath, 'r');

        // todo: error handling (import validation)
        /*
            student_number
            first_name
            middle_name
            last_name
            email
            wordpress_name
            wordpress_email
        */
        
        // originally, header row is key=>value
        // from CSV, this means index=>column_name (e.g. 0=>student_number)
        // array_flip swaps key and value, which means column_name=>index (e.g. student_number=>0)
        $headers = array_flip(fgetcsv($studentsCsv));
        
        // loop through every student in row
        while (($studentCsvRow = fgetcsv($studentsCsv)) !== FALSE) {      
            $new_student = new Student();
            $new_student->student_number = $studentCsvRow[$headers['student_number']];
            $new_student->supervisor_id = null;
            $new_student->faculty_id = null;
            $new_student->wordpress_name = $studentCsvRow[$headers['wordpress_name']];
            $new_student->wordpress_email = $studentCsvRow[$headers['wordpress_email']];
            $new_student->save();

            $new_user = new User();
            $new_user->role = User::ROLE_STUDENT;
            $new_user->role_id = $new_student->id;
            $new_user->first_name = $studentCsvRow[$headers['first_name']];
            $new_user->middle_name = $studentCsvRow[$headers['middle_name']] ?? '';
            $new_user->last_name = $studentCsvRow[$headers['last_name']];
            $new_user->email = $studentCsvRow[$headers['email']];
            $new_user->save();
        }
    }

    // ---

    public function showSupervisorCsvUploadForm(): Response
    {
        return Inertia::render('list/UploadSupervisors');
    }

    public function submitSupervisorCsv(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_FACULTY && $user->role !== User::ROLE_ADMIN) {
            abort(401);
        }

        // validate CSV MIME type
        // worst case, CSV has MIME type text/plain, which is basically the same as a regular .txt file
        // given a CSV file 1.csv, if it is renamed to 1.txt with no changes in data, then it would still be acceptable
        $request->validate([
            'file' => ['required', 'mimes:csv,txt'],
        ]);

        $filepath = $request->file('file')->store('list/supervisors');
        self::deleteAllSupervisors();
        self::importSupervisors($filepath);

        // todo: add confirmation? view csv before proceeding with upload?
        return redirect('/dashboard/admin/supervisors');
    }

    // todo: possibly move to AdminController?
    public function deleteAllSupervisors(): void
    {
        $supervisor_ids = DB::table('users')
            ->where('role', 'supervisor')
            ->pluck('role_id');
        
        $admin_controller = new AdminController();
        foreach ($supervisor_ids as $supervisor_id) {
            $admin_controller->deleteSupervisor($supervisor_id);
        }
    }

    public function importSupervisors(string $csvPath): void
    {
        // todo: clean up CSV importing (esp for non-local) (this path is not very good)
        $supervisorsCsv = fopen('../storage/app/private/' . $csvPath, 'r');

        // todo: error handling (import validation)
        /*
            first_name
            middle_name
            last_name
            email
        */
        
        // originally, header row is key=>value
        // from CSV, this means index=>column_name (e.g. 0=>first_name)
        // array_flip swaps key and value, which means column_name=>index (e.g. first_name=>0)
        $headers = array_flip(fgetcsv($supervisorsCsv));

        // loop through every supervisor in row
        while (($supervisorCsvRow = fgetcsv($supervisorsCsv)) !== FALSE) {      
            $new_supervisor = new Supervisor();
            $new_supervisor->save();

            $new_user = new User();
            $new_user->role = User::ROLE_SUPERVISOR;
            $new_user->role_id = $new_supervisor->id;
            $new_user->first_name = $supervisorCsvRow[$headers['first_name']];
            $new_user->middle_name = $supervisorCsvRow[$headers['middle_name']] ?? '';
            $new_user->last_name = $supervisorCsvRow[$headers['last_name']];
            $new_user->email = $supervisorCsvRow[$headers['email']];
            $new_user->save();
        }
    }

    // ---

    public function showFacultyCsvUploadForm(): Response
    {
        // todo: make UploadFaculties page
        return Inertia::render('list/UploadFaculties');
    }

    public function submitFacultyCsv(Request $request): RedirectResponse
    {   
        // todo: confirm if faculty should be allowed to add other faculty
        $user = Auth::user();
        if ($user->role !== User::ROLE_FACULTY && $user->role !== User::ROLE_ADMIN) {
            abort(401);
        }

        // validate CSV MIME type
        // worst case, CSV has MIME type text/plain, which is basically the same as a regular .txt file
        // given a CSV file 1.csv, if it is renamed to 1.txt with no changes in data, then it would still be acceptable
        $request->validate([
            'file' => ['required', 'mimes:csv,txt'],
        ]);

        $filepath = $request->file('file')->store('list/faculties');
        self::deleteAllFaculties();
        self::importFaculties($filepath);

        // todo: add confirmation? view csv before proceeding with upload?
        return redirect('/dashboard/admin/faculties');
    }

    // todo: possibly move to AdminController?
    public function deleteAllFaculties(): void
    {
        $faculty_ids = DB::table('users')
            ->where('role', 'faculty')
            ->pluck('role_id');
        
        $admin_controller = new AdminController();
        foreach ($faculty_ids as $faculty_id) {
            $admin_controller->deleteFaculty($faculty_id);
        }
    }

    public function importFaculties(string $csvPath): void
    {
        // todo: clean up CSV importing (esp for non-local) (this path is not very good)
        $facultiesCsv = fopen('../storage/app/private/' . $csvPath, 'r');

        // todo: error handling (import validation)
        /*
            first_name
            middle_name
            last_name
            section
            email
        */
        
        // originally, header row is key=>value
        // from CSV, this means index=>column_name (e.g. 0=>first_name)
        // array_flip swaps key and value, which means column_name=>index (e.g. first_name=>0)
        $headers = array_flip(fgetcsv($facultiesCsv));

        // loop through every faculty in row
        while (($facultyCsvRow = fgetcsv($facultiesCsv)) !== FALSE) {      
            $new_faculty = new Faculty();
            $new_faculty->section = $facultyCsvRow[$headers['section']];
            $new_faculty->save();

            $new_user = new User();
            $new_user->role = User::ROLE_FACULTY;
            $new_user->role_id = $new_faculty->id;
            $new_user->first_name = $facultyCsvRow[$headers['first_name']];
            $new_user->middle_name = $facultyCsvRow[$headers['middle_name']] ?? '';
            $new_user->last_name = $facultyCsvRow[$headers['last_name']];
            $new_user->email = $facultyCsvRow[$headers['email']];
            $new_user->save();
        }
    }   

    // ---

    public function showCompanyCsvUploadForm(): Response
    {
        // todo: make UploadCompanies page
        return Inertia::render('list/UploadCompanies');
    }

    public function submitCompanyCsv(Request $request): RedirectResponse
    {   
        $user = Auth::user();
        if ($user->role !== User::ROLE_FACULTY && $user->role !== User::ROLE_ADMIN) {
            abort(401);
        }

        // validate CSV MIME type
        // worst case, CSV has MIME type text/plain, which is basically the same as a regular .txt file
        // given a CSV file 1.csv, if it is renamed to 1.txt with no changes in data, then it would still be acceptable
        $request->validate([
            'file' => ['required', 'mimes:csv,txt'],
        ]);

        $filepath = $request->file('file')->store('list/companies');
        self::deleteAllCompanies();
        self::importCompanies($filepath);

        // todo: add confirmation? view csv before proceeding with upload?
        return redirect('/dashboard/admin/companies');
    }

    // todo: possibly move to AdminController?
    public function deleteAllCompanies(): void
    {
        $company_ids = DB::table('companies')
            ->pluck('id');
        
        $admin_controller = new AdminController();
        foreach ($company_ids as $company_id) {
            $admin_controller->deleteCompany($company_id);
        }
    }

    public function importCompanies(string $csvPath): void
    {
        // todo: clean up CSV importing (esp for non-local) (this path is not very good)
        $companiesCsv = fopen('../storage/app/private/' . $csvPath, 'r');

        // todo: error handling (import validation)
        /*
            company_name
        */
        
        // originally, header row is key=>value
        // from CSV, this means index=>column_name (e.g. 0=>first_name)
        // array_flip swaps key and value, which means column_name=>index (e.g. first_name=>0)
        $headers = array_flip(fgetcsv($companiesCsv));

        // loop through every company in row
        while (($companyCsvRow = fgetcsv($companiesCsv)) !== FALSE) {      
            $new_company = new Company();
            $new_company->company_name = $companyCsvRow[$headers['company_name']];
            $new_company->save();
        }
    }   
}
