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
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ImportsController extends Controller
{
    public function getCsvHeaders($csv)
    {
        // originally, header row is key=>value
        // from CSV, this means index=>column_name (e.g. 0=>student_number)
        // array_flip swaps key and value, which means column_name=>index (e.g. student_number=>0)
        $headers = array_flip(fgetcsv($csv));

        return $headers;
    }

    public function csvToCollection($csv)
    {
        // get headers of CSV
        $headers = self::getCsvHeaders($csv);

        // loop through every row in CSV
        $collection = [];
        while (($csv_row = fgetcsv($csv)) !== FALSE) {
            // skip CSV row if it is shorter than the header row
            if (count($csv_row) < count($headers)) {
                continue;
            }

            // ---

            $collection_row = array();

            foreach ($headers as $key => $value) {
                $collection_row[$key] = $csv_row[$value];
            }

            array_push($collection, $collection_row);
        }

        return collect($collection);
    }

    public function validateCsv($csvPath, $primary_keys, $unique_keys, $other_keys_required, $other_keys_optional): bool
    {
        // todo: clean up CSV importing (esp for non-local) (this path is not very good)
        $importedCsv = fopen('../storage/app/private/' . $csvPath, 'r');
        $importedCsvHeaders = self::getCsvHeaders($importedCsv);
        //error_log($importedCsvHeaders[0]);

        $importedCsv = fopen('../storage/app/private/' . $csvPath, 'r');
        $importedCollection = self::csvToCollection($importedCsv);

        // check if primary keys are:
        // present in CSV headers
        // not null
        // unique
        foreach ($primary_keys as $primary_key) {
            if (!array_key_exists($primary_key, $importedCsvHeaders)) {
                return false;
            }

            foreach ($importedCollection as $importedRow) {
                if ($importedRow[$primary_key] == NULL) {
                    return false;
                }
            }

            if (count($importedCollection->duplicatesStrict($primary_key)) > 0) {
                return false;
            }
        }

        // check if unique keys are:
        // present in CSV headers
        // unique
        foreach ($unique_keys as $unique_key) {
            if (!array_key_exists($unique_key, $importedCsvHeaders)) {
                return false;
            }

            if (count($importedCollection->duplicatesStrict($unique_key)) > 0) {
                return false;
            }
        }

        // check if other required keys are:
        // present in CSV headers
        // not null
        foreach ($other_keys_required as $other_key) {
            if (!array_key_exists($other_key, $importedCsvHeaders)) {
                return false;
            }

            foreach ($importedCollection as $importedRow) {
                if ($importedRow[$other_key] == NULL) {
                    return false;
                }
            }
        }

        // check if other optional keys are
        // present in CSV headers
        foreach ($other_keys_optional as $other_key) {
            if (!array_key_exists($other_key, $importedCsvHeaders)) {
                return false;
            }
        }

        // CSV is valid if none of the above guard clauses are hit
        return true;
    }

    // ---

    public function showImportStudents(): Response
    {
        return Inertia::render('import/UploadStudents');
    }
    
    public function showAddMultipleStudents(): Response
    {
        return Inertia::render('add-multiple/UploadStudents');
    }

    public function importStudents(Request $request): RedirectResponse
    {
        return self::submitStudentCsv($request, true);
    }

    public function addMultipleStudents(Request $request): RedirectResponse
    {
        return self::submitStudentCsv($request, true);
    }

    public function submitStudentCsv(Request $request, bool $clearStudents): RedirectResponse
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

        $filepath = $request->file('file')->store('import/students');

        // ---

        /*
            student_number      primary
            first_name          other_reqd
            middle_name         other_opt
            last_name           other_reqd
            email               unique
            wordpress_name      other_reqd
            wordpress_email     unique
        */

        // check CSV for validity of values under primary/unique keys
        $primary_keys = ['student_number'];
        $unique_keys = ['email', 'wordpress_email'];
        $other_keys_required = ['first_name', 'last_name', 'wordpress_name'];
        $other_keys_optional = ['middle_name'];
        if (!self::validateCsv($filepath, $primary_keys, $unique_keys, $other_keys_required, $other_keys_optional)) {
            return back()->withErrors(['file' => 'Cannot read file. Please check its formatting.'])->with('error', 'Cannot read file. Please check its formatting.');
        }

        // todo: check foreign keys
        $foreign_keys = ['section', 'supervisor_name'];

        // ---
        
        // replace current database with CSV if valid
        if ($clearStudents) {
            self::deleteAllStudents();
        }

        self::addStudentsFromCsv($filepath);

        // todo: add confirmation? view csv before proceeding with upload?
        return redirect('/dashboard/students')->with('success', 'Successfully imported student list.');
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

    public function addStudentsFromCsv($csvPath): void
    {
        // todo: clean up CSV importing (esp for non-local) (this path is not very good)
        $studentsCsv = fopen('../storage/app/private/' . $csvPath, 'r');
        $studentsCollection = self::csvToCollection($studentsCsv);

        // loop through every student in row
        foreach ($studentsCollection as $studentRow) {
            $new_student = new Student();
            $new_student->student_number = $studentRow['student_number'];
            $new_student->supervisor_id = null;
            $new_student->faculty_id = null;
            $new_student->wordpress_name = $studentRow['wordpress_name'];
            $new_student->wordpress_email = $studentRow['wordpress_email'];
            $new_student->save();

            $new_user = new User();
            $new_user->role = User::ROLE_STUDENT;
            $new_user->role_id = $new_student->id;
            $new_user->first_name = $studentRow['first_name'];
            $new_user->middle_name = $studentRow['middle_name'] ?? '';
            $new_user->last_name = $studentRow['last_name'];
            $new_user->email = $studentRow['email'];
            $new_user->save();
        }
    }

    // ---

    public function showImportSupervisors(): Response
    {
        return Inertia::render('import/UploadSupervisors');
    }
    
    public function showAddMultipleSupervisors(): Response
    {
        return Inertia::render('add-multiple/UploadSupervisors');
    }

    public function importSupervisors(Request $request): RedirectResponse
    {
        return self::submitSupervisorCsv($request, true);
    }

    public function addMultipleSupervisors(Request $request): RedirectResponse
    {
        return self::submitSupervisorCsv($request, true);
    }

    public function submitSupervisorCsv(Request $request, bool $clearSupervisors): RedirectResponse
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

        $filepath = $request->file('file')->store('import/supervisors');

        // ---

        /*
            first_name      other_reqd
            middle_name     other_opt
            last_name       other_reqd
            email           unique
        */

        // check CSV for validity of values under primary/unique keys
        $primary_keys = [];
        $unique_keys = ['email'];
        $other_keys_required = ['first_name', 'last_name'];
        $other_keys_optional = ['middle_name'];
        if (!self::validateCsv($filepath, $primary_keys, $unique_keys, $other_keys_required, $other_keys_optional)) {
            return back()->withErrors(['file' => 'Cannot read file. Please check its formatting.'])->with('error', 'Cannot read file. Please check its formatting.');
        }

        // todo: check foreign keys
        $foreign_keys = ['company_name'];

        // ---

        // replace current database with CSV if valid
        if ($clearSupervisors) {  
            self::deleteAllSupervisors();
        }

        self::addSupervisorsFromCsv($filepath);

        // todo: add confirmation? view csv before proceeding with upload?
        return redirect('/dashboard/supervisors')->with('success', 'Successfully imported supervisor list.');
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

    public function addSupervisorsFromCsv(string $csvPath): void
    {
        // todo: clean up CSV importing (esp for non-local) (this path is not very good)
        $supervisorsCsv = fopen('../storage/app/private/' . $csvPath, 'r');
        $supervisorsCollection = self::csvToCollection($supervisorsCsv);

        // loop through every supervisor in CSV
        foreach ($supervisorsCollection as $supervisorRow) {
            $new_supervisor = new Supervisor();
            $new_supervisor->save();

            $new_user = new User();
            $new_user->role = User::ROLE_SUPERVISOR;
            $new_user->role_id = $new_supervisor->id;
            $new_user->first_name = $supervisorRow['first_name'];
            $new_user->middle_name = $supervisorRow['middle_name'] ?? '';
            $new_user->last_name = $supervisorRow['last_name'];
            $new_user->email = $supervisorRow['email'];
            $new_user->save();
        }
    }

    // ---

    public function showImportFaculties(): Response
    {
        return Inertia::render('import/UploadFaculties');
    }
    
    public function showAddMultipleFaculties(): Response
    {
        return Inertia::render('add-multiple/UploadFaculties');
    }

    public function importFaculties(Request $request): RedirectResponse
    {
        return self::submitFacultyCsv($request, true);
    }

    public function addMultipleFaculties(Request $request): RedirectResponse
    {
        return self::submitFacultyCsv($request, true);
    }


    public function submitFacultyCsv(Request $request, bool $clearFaculties): RedirectResponse
    {
        // todo: confirm if faculty should be allowed to add other faculty
        $user = Auth::user();
        if ($user->role !== User::ROLE_ADMIN) {
            abort(401);
        }

        // validate CSV MIME type
        // worst case, CSV has MIME type text/plain, which is basically the same as a regular .txt file
        // given a CSV file 1.csv, if it is renamed to 1.txt with no changes in data, then it would still be acceptable
        $request->validate([
            'file' => ['required', 'mimes:csv,txt'],
        ]);

        $filepath = $request->file('file')->store('import/faculties');

        // ---

        /*
            first_name      other_reqd
            middle_name     other_opt
            last_name       other_reqd
            section         foreign
            email           unique
        */

        // check CSV for validity of values under primary/unique keys
        $primary_keys = [];
        $unique_keys = ['email'];
        $other_keys_required = ['first_name', 'last_name'];
        $other_keys_optional = ['middle_name', 'section'];
        if (!self::validateCsv($filepath, $primary_keys, $unique_keys, $other_keys_required, $other_keys_optional)) {
            return back()->withErrors(['file' => 'Cannot read file. Please check its formatting.'])->with('error', 'Cannot read file. Please check its formatting.');
        }

        // faculty list has no foreign keys
        //$foreign_keys = [];

        // ---

        // replace current database with CSV if valid
        if ($clearFaculties) {
            self::deleteAllFaculties();
        }

        self::addFacultiesFromCsv($filepath);

        // todo: add confirmation? view csv before proceeding with upload?
        return redirect('/dashboard/faculties')->with('success', 'Successfully imported faculty list.');
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

    public function addFacultiesFromCsv(string $csvPath): void
    {
        // todo: clean up CSV importing (esp for non-local) (this path is not very good)
        $facultiesCsv = fopen('../storage/app/private/' . $csvPath, 'r');
        $facultiesCollection = self::csvToCollection($facultiesCsv);

        // loop through every faculty in row
        foreach ($facultiesCollection as $facultyRow) {
            $new_faculty = new Faculty();
            $new_faculty->section = $facultyRow['section'];
            $new_faculty->save();

            $new_user = new User();
            $new_user->role = User::ROLE_FACULTY;
            $new_user->role_id = $new_faculty->id;
            $new_user->first_name = $facultyRow['first_name'];
            $new_user->middle_name = $facultyRow['middle_name'] ?? '';
            $new_user->last_name = $facultyRow['last_name'];
            $new_user->email = $facultyRow['email'];
            $new_user->save();
        }
    }

    // ---

    public function showImportCompanies(): Response
    {
        return Inertia::render('import/UploadCompanies');
    }
    
    public function showAddMultipleCompanies(): Response
    {
        return Inertia::render('add-multiple/UploadCompanies');
    }

    public function importCompanies(Request $request): RedirectResponse
    {
        return self::submitCompanyCsv($request, true);
    }

    public function addMultipleCompanies(Request $request): RedirectResponse
    {
        return self::submitCompanyCsv($request, true);
    }

    public function submitCompanyCsv(Request $request, bool $clearCompanies): RedirectResponse
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

        $filepath = $request->file('file')->store('import/companies');

        // ---

        /*
            company_name    unique
        */

        // check CSV for validity of values under primary/unique keys
        $primary_keys = [];
        $unique_keys = ['company_name'];
        $other_keys_required = [];
        $other_keys_optional = [];
        if (!self::validateCsv($filepath, $primary_keys, $unique_keys, $other_keys_required, $other_keys_optional)) {
            return back()->withErrors(['file' => 'Cannot read file. Please check its formatting.'])->with('error', 'Cannot read file. Please check its formatting.');
        }

        // company list has no foreign keys
        //$foreign_keys = [];

        // ---

        // replace current database with CSV if valid
        if ($clearCompanies) {
            self::deleteAllCompanies();
        }
        
        self::addCompaniesFromCsv($filepath);

        // todo: add confirmation? view csv before proceeding with upload?
        return redirect('/dashboard/companies')->with('success', 'Successfully imported company list.');
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

    public function addCompaniesFromCsv(string $csvPath): void
    {
        // todo: clean up CSV importing (esp for non-local) (this path is not very good)
        $companiesCsv = fopen('../storage/app/private/' . $csvPath, 'r');
        $companiesCollection = self::csvToCollection($companiesCsv);

        // loop through every company in CSV
        foreach ($companiesCollection as $companyRow) {
            $new_company = new Company();
            $new_company->company_name = $companyRow['company_name'];
            $new_company->save();
        }
    }
}
