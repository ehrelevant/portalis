<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use PhpOption\None;

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

        // todo: validate CSV type, but be flexible with MIME type
        // worst case, CSV has MIME type text/plain, which is basically the same as a regular .txt file
        $request->validate([
            'file' => ['required', 'mimes:csv,txt'],
        ]);

        $filepath = $request->file('file');
        self::importStudents($filepath);

        // todo: add confirmation? view csv before proceeding with upload?
        return redirect('/dashboard/admin/students');
    }

    public function importStudents(string $csvPath): void
    {   
        // todo: add actual CSV import logic
        /*
        //$studentsCsv = fopen($csvPath, 'r');
        //fclose($studentsCsv);

        $test = fopen('test.csv', 'w');
        fputcsv($test, ['hello world']);
        fputcsv($test, [$csvPath]);
        fclose($test);
        */
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

        $filepath = $request->file('file');
        self::importStudents($filepath);

        // todo: add confirmation? view csv before proceeding with upload?
        return redirect('/dashboard/admin/supervisors');
    }

    public function importSupervisors(string $csvPath): void
    {

    }
}
