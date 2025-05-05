<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Faculty;
use App\Models\FormStatus;
use App\Models\Student;
use App\Models\SubmissionStatus;
use App\Models\Supervisor;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

enum CsvRowStatus {
    case SUCCESSFUL;
    case DUPLICATE;
    case ERROR;
}
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

    public function csvToCollection($csv): array
    {
        // get headers of CSV
        $headers = self::getCsvHeaders($csv);

        // loop through every row in CSV
        $collection = [];
        $num_errors = 0;
        while (($csv_row = fgetcsv($csv)) !== FALSE) {
            // skip CSV row if it is shorter than the header row
            if (count($csv_row) < count($headers)) {
                // count the number of skipped rows
                $num_errors++;
                continue;
            }

            // ---

            $collection_row = array();

            foreach ($headers as $key => $value) {
                $collection_row[$key] = $csv_row[$value];
            }

            array_push($collection, $collection_row);
        }

        return [
            'collection'=>collect($collection),
            'num_errors'=>$num_errors
        ];
    }

    // todo: this can be simplified once Tagged Unions are added to PHP one day, similar to the Result type in Rust
    public function validateCsvHeaders(string $csvPath, array $keys): bool
    {
        $importedCsv = fopen('../storage/app/private/' . $csvPath, 'r');
        $importedCsvHeaders = self::getCsvHeaders($importedCsv);

        foreach ($keys as $key) {
            if (!array_key_exists($key, $importedCsvHeaders)) {
                return false;
            }
        }

        return true;
    }

    public function validateCsv(string $csvPath, int $year, array $primary_keys, array $unique_keys, array $other_keys_required, Collection $existingDatabase)
    {
        $importedCsv = fopen('../storage/app/private/' . $csvPath, 'r');
        $importedCollectionRaw = self::csvToCollection($importedCsv);
        $importedCollection = $importedCollectionRaw['collection'];

        $csvLog = [
            'successful'=>collect(),
            'num_duplicates'=>0,
            'num_errors'=>$importedCollectionRaw['num_errors']
        ];

        foreach ($importedCollection as $importedRow) {
            $csvRowStatus = CsvRowStatus::SUCCESSFUL;
            
            // check if primary keys are:
            // not null
            // unique
            foreach ($primary_keys as $primary_key) {
                if ($importedRow[$primary_key] == NULL) {
                    $csvRowStatus = CsvRowStatus::ERROR;
                    goto tallyCsvRowStatus;
                }

                foreach ($existingDatabase as $existingRow) {
                    if ($existingRow->$primary_key == $importedRow[$primary_key]) {
                        $csvRowStatus = CsvRowStatus::DUPLICATE;
                        goto tallyCsvRowStatus;
                    }
                }
                
                foreach ($csvLog['successful'] as $successfulRow) {
                    if ($successfulRow[$primary_key] == $importedRow[$primary_key]) {
                        $csvRowStatus = CsvRowStatus::DUPLICATE;
                        goto tallyCsvRowStatus;
                    }
                }
            }

            // check if unique keys are:
            // unique
            foreach ($unique_keys as $unique_key) {
                foreach ($existingDatabase as $existingRow) {
                    if ($existingRow->$unique_key == $importedRow[$unique_key]) {
                        $csvRowStatus = CsvRowStatus::DUPLICATE;
                        goto tallyCsvRowStatus;
                    }
                }

                foreach ($csvLog['successful'] as $successfulRow) {
                    if ($successfulRow[$unique_key] == $importedRow[$unique_key]) {
                        $csvRowStatus = CsvRowStatus::DUPLICATE;
                        goto tallyCsvRowStatus;
                    }
                }
            }
            
            // check if other required keys are:
            // not null
            foreach ($other_keys_required as $other_key) {
                if ($importedRow[$other_key] == NULL) {
                    $csvRowStatus = CsvRowStatus::ERROR;
                    goto tallyCsvRowStatus;
                }
            }

            // tally status of CSV row into csvLog
            tallyCsvRowStatus:
            switch ($csvRowStatus) {
                case CsvRowStatus::SUCCESSFUL:
                    $csvLog['successful']->push($importedRow);
                    break;
                case CsvRowStatus::DUPLICATE:
                    $csvLog['num_duplicates']++;
                    break;
                case CsvRowStatus::ERROR:
                    $csvLog['num_errors']++;
                    break;
            }
        }

        return $csvLog;
    }

    public function validateCollectionValues(array $inputCsvLog, array $email_keys)
    {
        $csvLog = [
            'successful'=>collect(),
            'num_duplicates'=>$inputCsvLog['num_duplicates'],
            'num_errors'=>$inputCsvLog['num_errors']
        ];

        foreach ($inputCsvLog['successful'] as $importedRow) {
            $csvRowStatus = CsvRowStatus::SUCCESSFUL;

            // check if email keys are valid emails
            foreach ($email_keys as $email_key) {
                if (!filter_var($importedRow[$email_key], FILTER_VALIDATE_EMAIL)) {
                    $csvRowStatus = CsvRowStatus::ERROR;
                    goto tallyCsvRowStatus;
                }
            }

            tallyCsvRowStatus:
            switch ($csvRowStatus) {
                case CsvRowStatus::SUCCESSFUL:
                    $csvLog['successful']->push($importedRow);
                    break;
                case CsvRowStatus::DUPLICATE:
                    $csvLog['num_duplicates']++;
                    break;
                case CsvRowStatus::ERROR:
                    $csvLog['num_errors']++;
                    break;
            }
        }

        return $csvLog;
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
        return self::submitStudentCsv($request, false);
    }

    public function submitStudentCsv(Request $request, bool $clearStudents): RedirectResponse
    {
        try {
            $user = Auth::user();
            if ($user->role !== User::ROLE_FACULTY && $user->role !== User::ROLE_ADMIN) {
                abort(401);
            }
            
            // validate CSV MIME type
            // worst case, CSV has MIME type text/plain, which is basically the same as a regular .txt file
            // given a CSV file 1.csv, if it is renamed to 1.txt with no changes in data, then it would still be acceptable
            $request->validate([
                'file' => ['required', 'mimes:csv,txt'],
                'year' => ['required', 'integer'],
            ]);
            
            $filepath = $request->file('file')->store('import/students');
            $year = $request->input('year', date("Y"));
            
            // ---
            
            /*
                student_number      primary
                first_name          other_reqd
                middle_name         other_null
                last_name           other_reqd
                email               unique
                wordpress_name      other_reqd
                wordpress_email     unique
            */
            
            // relevant keys for importing students
            $primary_keys = ['student_number'];
            $unique_keys = ['email', 'wordpress_email'];
            $other_keys_required = ['first_name', 'last_name', 'wordpress_name'];
            $other_keys_nullable = ['middle_name'];
            
            // get existing primary/unique keys from database
            $existingStudents = DB::table('users')
                ->where('year', $year)
                ->where('role', 'student')
                ->join('students', 'users.role_id', '=', 'students.id')
                ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')
                ->select(
                    'students.student_number',
                    'users.email',
                    'students.wordpress_email',
                )
                ->get();
            
            // check if all primary/unique/other keys are present in CSV headers
            $keys = array_merge($primary_keys, $unique_keys, $other_keys_required, $other_keys_nullable);
            if (!self::validateCsvHeaders($filepath, $keys)) {
                return back()->withErrors(['file' => 'Cannot read file. Please check its formatting.'])->with('error', 'Cannot read file. Please check its formatting.');
            }
            
            // todo: check foreign keys
            $foreign_keys = ['section', 'supervisor_name'];

            // relevant keys for value validation
            $email_keys = ['email', 'wordpress_email'];
            
            // ---
            
            // replace current database with CSV if valid, ignoring existing values
            if ($clearStudents) {
                self::deleteAllStudents($year);
                $existingStudents = collect();
            }
            
            $csvStats = self::validateCsv($filepath, $year, $primary_keys, $unique_keys, $other_keys_required, $existingStudents);
            $csvStats = self::validateCollectionValues($csvStats, $email_keys);
            self::addStudentsFromCollection($csvStats['successful'], $year);
            
            if ($clearStudents) {
                return redirect('/dashboard/students')
                    ->with('success', 'Successfully imported ' . $csvStats['successful']->count() . ' students.
                    The CSV contained ' . $csvStats['num_duplicates'] . ' duplicate entries and ' . $csvStats['num_errors'] . ' errors.');
            } else {
                return redirect('/dashboard/students')
                    ->with('success', 'Successfully added ' . $csvStats['successful']->count() . ' students.
                    The CSV contained ' . $csvStats['num_duplicates'] . ' duplicate entries and ' . $csvStats['num_errors'] . ' errors.');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Failed to import students from CSV file.');
        }
    }

    // todo: possibly move to AdminController?
    public function deleteAllStudents(int $year): void
    {
        $student_ids = DB::table('users')
            ->where('year', $year)
            ->where('role', 'student')
            ->pluck('role_id');

        $admin_controller = new AdminController();
        foreach ($student_ids as $student_id) {
            $admin_controller->deleteStudent($student_id);
        }
    }

    public function addStudentsFromCollection(Collection $studentsCollection, int $year): void
    {
        // loop through every student in row
        foreach ($studentsCollection as $studentRow) {
            $new_student = new Student();
            $new_student->student_number = $studentRow['student_number'];
            $new_student->supervisor_id = null;
            $new_student->faculty_id = null;
            /*
            $new_student->supervisor_id = $values['supervisor_id'];
            if ($values['section']) {
                $new_student->faculty_id = DB::table('faculties')
                    ->where('section', $values['section'])
                    ->firstOrFail()
                    ->id;
            } else {
                $new_student->faculty_id = null;
            }
            */
            $new_student->wordpress_name = $studentRow['wordpress_name'];
            $new_student->wordpress_email = $studentRow['wordpress_email'];
            $new_student->save();

            $new_user = new User();
            $new_user->year = $year;
            $new_user->role = User::ROLE_STUDENT;
            $new_user->role_id = $new_student->id;
            $new_user->first_name = $studentRow['first_name'];
            $new_user->middle_name = $studentRow['middle_name'] ?? '';
            $new_user->last_name = $studentRow['last_name'];
            $new_user->email = $studentRow['email'];
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
        return self::submitSupervisorCsv($request, false);
    }

    public function submitSupervisorCsv(Request $request, bool $clearSupervisors): RedirectResponse
    {
        try {
            $user = Auth::user();
            if ($user->role !== User::ROLE_FACULTY && $user->role !== User::ROLE_ADMIN) {
                abort(401);
            }

            // validate CSV MIME type
            // worst case, CSV has MIME type text/plain, which is basically the same as a regular .txt file
            // given a CSV file 1.csv, if it is renamed to 1.txt with no changes in data, then it would still be acceptable
            $request->validate([
                'file' => ['required', 'mimes:csv,txt'],
                'year' => ['required', 'integer'],
            ]);

            $filepath = $request->file('file')->store('import/supervisors');
            $year = $request->input('year', date("Y"));

            // ---

            /*
                first_name      other_reqd
                middle_name     other_null
                last_name       other_reqd
                email           unique
            */

            // relevant keys for importing supervisors
            $primary_keys = [];
            $unique_keys = ['email'];
            $other_keys_required = ['first_name', 'last_name'];
            $other_keys_nullable = ['middle_name'];

            // get existing primary/unique keys from database
            $existingSupervisors = DB::table('users')
                ->where('year', $year)
                ->where('role', 'supervisor')
                ->select(
                    'users.email',
                )
                ->get();

            // check if all primary/unique/other keys are present in CSV headers
            $keys = array_merge($primary_keys, $unique_keys, $other_keys_required, $other_keys_nullable);
            if (!self::validateCsvHeaders($filepath, $keys)) {
                return back()->withErrors(['file' => 'Cannot read file. Please check its formatting.'])->with('error', 'Cannot read file. Please check its formatting.');
            }

            // todo: check foreign keys
            $foreign_keys = ['company_name'];

            // relevant keys for value validation
            $email_keys = ['email'];

            // ---

            // replace current database with CSV if valid, ignoring existing values
            if ($clearSupervisors) {  
                self::deleteAllSupervisors($year);
                $existingSupervisors = collect();
            }

            $csvStats = self::validateCsv($filepath, $year, $primary_keys, $unique_keys, $other_keys_required, $existingSupervisors);
            $csvStats = self::validateCollectionValues($csvStats, $email_keys);
            self::addSupervisorsFromCollection($csvStats['successful'], $year);

            if ($clearSupervisors) {
                return redirect('/dashboard/supervisors')
                    ->with('success', 'Successfully imported ' . $csvStats['successful']->count() . ' supervisors.
                    The CSV contained ' . $csvStats['num_duplicates'] . ' duplicate entries and ' . $csvStats['num_errors'] . ' errors.');
            } else {
                return redirect('/dashboard/supervisors')
                    ->with('success', 'Successfully added ' . $csvStats['successful']->count() . ' supervisors.
                    The CSV contained ' . $csvStats['num_duplicates'] . ' duplicate entries and ' . $csvStats['num_errors'] . ' errors.');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Failed to import supervisors from CSV file.');
        }
    }

    // todo: possibly move to AdminController?
    public function deleteAllSupervisors(int $year): void
    {
        $supervisor_ids = DB::table('users')
            ->where('year', $year)
            ->where('role', 'supervisor')
            ->pluck('role_id');

        $admin_controller = new AdminController();
        foreach ($supervisor_ids as $supervisor_id) {
            $admin_controller->deleteSupervisor($supervisor_id);
        }
    }

    public function addSupervisorsFromCollection(Collection $supervisorsCollection, int $year): void
    {
        // loop through every supervisor in CSV
        foreach ($supervisorsCollection as $supervisorRow) {
            $new_supervisor = new Supervisor();
            //$new_supervisor->company_id = $supervisorRow['company_id'];
            $new_supervisor->save();

            $new_user = new User();
            $new_user->year = $year;
            $new_user->role = User::ROLE_SUPERVISOR;
            $new_user->role_id = $new_supervisor->id;
            $new_user->first_name = $supervisorRow['first_name'];
            $new_user->middle_name = $supervisorRow['middle_name'] ?? '';
            $new_user->last_name = $supervisorRow['last_name'];
            $new_user->email = $supervisorRow['email'];
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
        return self::submitFacultyCsv($request, false);
    }


    public function submitFacultyCsv(Request $request, bool $clearFaculties): RedirectResponse
    {
        try {
            $user = Auth::user();
            if ($user->role !== User::ROLE_ADMIN) {
                abort(401);
            }

            // validate CSV MIME type
            // worst case, CSV has MIME type text/plain, which is basically the same as a regular .txt file
            // given a CSV file 1.csv, if it is renamed to 1.txt with no changes in data, then it would still be acceptable
            $request->validate([
                'file' => ['required', 'mimes:csv,txt'],
                'year' => ['required', 'integer'],
            ]);

            $filepath = $request->file('file')->store('import/faculties');
            $year = $request->input('year', date("Y"));

            // ---

            /*
                first_name      other_reqd
                middle_name     other_null
                last_name       other_reqd
                section         foreign
                email           unique
            */

            // relevant keys for importing faculties
            $primary_keys = [];
            $unique_keys = ['email'];
            $other_keys_required = ['first_name', 'last_name'];
            $other_keys_nullable = ['middle_name', 'section'];

            // get existing primary/unique keys from database
            $existingFaculties = DB::table('users')
                ->where('year', $year)
                ->where('role', 'supervisor')
                ->select(
                    'users.email',
                )
                ->get();

            // check if all primary/unique/other keys are present in CSV headers
            $keys = array_merge($primary_keys, $unique_keys, $other_keys_required, $other_keys_nullable);
            if (!self::validateCsvHeaders($filepath, $keys)) {
                return back()->withErrors(['file' => 'Cannot read file. Please check its formatting.'])->with('error', 'Cannot read file. Please check its formatting.');
            }

            // faculty list has no foreign keys
            //$foreign_keys = [];

            // relevant keys for value validation
            $email_keys = ['email'];

            // ---

            // replace current database with CSV if valid, ignoring existing values
            if ($clearFaculties) {
                self::deleteAllFaculties($year);
                $existingFaculties = collect();
            }

            $csvStats = self::validateCsv($filepath, $year, $primary_keys, $unique_keys, $other_keys_required, $existingFaculties);
            $csvStats = self::validateCollectionValues($csvStats, $email_keys);
            self::addFacultiesFromCollection($csvStats['successful'], $year);

            if ($clearFaculties) {
                return redirect('/dashboard/faculties')
                    ->with('success', 'Successfully imported ' . $csvStats['successful']->count() . ' faculties.
                    The CSV contained ' . $csvStats['num_duplicates'] . ' duplicate entries and ' . $csvStats['num_errors'] . ' errors.');
            } else {
                return redirect('/dashboard/faculties')
                    ->with('success', 'Successfully added ' . $csvStats['successful']->count() . ' faculties.
                    The CSV contained ' . $csvStats['num_duplicates'] . ' duplicate entries and ' . $csvStats['num_errors'] . ' errors.');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Failed to import faculties from CSV file.');
        }
    }

    // todo: possibly move to AdminController?
    public function deleteAllFaculties(int $year): void
    {
        $faculty_ids = DB::table('users')
            ->where('year', $year)
            ->where('role', 'faculty')
            ->pluck('role_id');

        $admin_controller = new AdminController();
        foreach ($faculty_ids as $faculty_id) {
            $admin_controller->deleteFaculty($faculty_id);
        }
    }

    public function addFacultiesFromCollection(Collection $facultiesCollection, int $year): void
    {
        // loop through every faculty in row
        foreach ($facultiesCollection as $facultyRow) {
            $new_faculty = new Faculty();
            $new_faculty->section = $facultyRow['section'];
            $new_faculty->save();

            $new_user = new User();
            $new_user->year = $year;
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
        return self::submitCompanyCsv($request, false);
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
                'year' => ['required', 'integer'],
            ]);

            $filepath = $request->file('file')->store('import/companies');
            $year = $request->input('year', date("Y"));

            // ---

            /*
                company_name    unique
            */

            // relevant keys for importing companies
            $primary_keys = [];
            $unique_keys = ['company_name'];
            $other_keys_required = [];
            $other_keys_nullable = [];

            // get existing primary/unique keys from database
            $existingCompanies = DB::table('companies')
                ->where('year', $year)
                ->select(
                    'companies.company_name',
                )
                ->get();

            // check if all primary/unique/other keys are present in CSV headers
            $keys = array_merge($primary_keys, $unique_keys, $other_keys_required, $other_keys_nullable);
            if (!self::validateCsvHeaders($filepath, $keys)) {
                return back()->withErrors(['file' => 'Cannot read file. Please check its formatting.'])->with('error', 'Cannot read file. Please check its formatting.');
            }

            // company list has no foreign keys
            //$foreign_keys = [];

            // relevant keys for value validation
            $email_keys = [];

            // ---

            // replace current database with CSV if valid, ignoring existing values
            if ($clearCompanies) {
                self::deleteAllCompanies($year);
                $existingCompanies = collect();
            }
            
            $csvStats = self::validateCsv($filepath, $year, $primary_keys, $unique_keys, $other_keys_required, $existingCompanies);
            //$csvStats = self::validateCollectionValues($csvStats, $email_keys);
            self::addCompaniesFromCollection($csvStats['successful'], $year);
            
            if ($clearCompanies) {
                return redirect('/dashboard/companies')
                    ->with('success', 'Successfully imported ' . $csvStats['successful']->count() . ' companies.
                    The CSV contained ' . $csvStats['num_duplicates'] . ' duplicate entries and ' . $csvStats['num_errors'] . ' errors.');
            } else {
                return redirect('/dashboard/companies')
                    ->with('success', 'Successfully added ' . $csvStats['successful']->count() . ' companies.
                    The CSV contained ' . $csvStats['num_duplicates'] . ' duplicate entries and ' . $csvStats['num_errors'] . ' errors.');
            }
    }

    // todo: possibly move to AdminController?
    public function deleteAllCompanies(int $year): void
    {
        $company_ids = DB::table('companies')
            ->where('year', $year)
            ->pluck('id');

        $admin_controller = new AdminController();
        foreach ($company_ids as $company_id) {
            $admin_controller->deleteCompany($company_id);
        }
    }

    public function addCompaniesFromCollection(Collection $companiesCollection, int $year): void
    {
        // loop through every company in CSV
        foreach ($companiesCollection as $companyRow) {
            $new_company = new Company();
            $new_company->year = $year;
            $new_company->company_name = $companyRow['company_name'];
            $new_company->save();
        }
    }
}
