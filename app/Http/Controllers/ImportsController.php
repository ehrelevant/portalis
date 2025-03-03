<?php

namespace App\Http\Controllers;

use App\Models\Student;
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

        // todo 1: allow dynamic order of columns in CSV
        // todo 2: error handling (import validation)
        /*
            student_number
            first_name
            middle_name
            last_name
            email
            wordpress_name
            wordpress_email
        */
        
        // skip header row; might also be usable for todo 1/2
        $studentCsvRow = fgetcsv($studentsCsv);
        
        $headers = array(
            'student_number' => 0,
            'first_name' => 1,
            'middle_name' => 2,
            'last_name' => 3,
            'email' => 4,
            'wordpress_name' => 5,
            'wordpress_email' => 6
        );

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

        // todo: validate CSV type, but be flexible with MIME type
        // worst case, CSV has MIME type text/plain, which is basically the same as a regular .txt file
        $request->validate([
            'file' => ['required', 'mimes:csv,txt'],
        ]);

        $filepath = $request->file('file')->store('/list/supervisors');
        self::importSupervisors($filepath);

        // todo: add confirmation? view csv before proceeding with upload?
        return redirect('/dashboard/admin/supervisors');
    }

    public function importSupervisors(string $csvPath): void
    {

    }
}
