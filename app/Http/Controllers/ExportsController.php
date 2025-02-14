<?php

namespace App\Http\Controllers;

use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportsController extends Controller
{
    public function exportStudentSections(): BinaryFileResponse
    {
        $dbTable = DB::table('users')
            ->where('role', 'student')
            // todo: confirm if dropped students (section="DRP") should be included in CSV
            ->where('students.has_dropped', '0')
            // todo: confirm if sectionless students (section=null) should be included in CSV
            ->where('faculties.section', '!=', 'null')
            ->join('students', 'users.role_id', '=', 'students.student_number')
            ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')
            ->select(
                'users.id AS user_id',
                'students.student_number',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'faculties.section',
                //'students.has_dropped',
            )
            ->orderBy('students.student_number')
            ->get();

        $csvFileName = 'student_sections.csv';

        // ---

        $csvFile = fopen($csvFileName, 'w');
        
        $headers = array_keys((array) $dbTable[0]);
        fputcsv($csvFile, $headers);

        foreach ($dbTable as $row) {
            fputcsv($csvFile, (array) $row);
        }

        fclose($csvFile);

        // ---
        
        // todo: show user prompt to download from public folder to actual local filesystem
        return response()->download(public_path($csvFileName));
    }

    /*
    public function exportMidsemReportStudents(): BinaryFileResponse
    {

    }

    public function exportFinalReportStudents(): BinaryFileResponse
    {
        
    }

    public function exportCompanyEvaluations(): BinaryFileResponse
    {
        
    }

    public function exportStudentAssessments(): BinaryFileResponse
    {
        
    }

    public function exportMidsemReportSupervisors(): BinaryFileResponse
    {
        
    }

    public function exportFinalReportSupervisors(): BinaryFileResponse
    {
        
    }
    */
}
