<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportsController extends Controller
{
    public function exportStudentSections(): BinaryFileResponse
    {
        $csvFileName = 'student_sections.csv';

        $dbTable = DB::table('users')
            ->where('role', 'student')
            // todo: confirm if dropped students (section="DRP") should be included in CSV
            ->where('students.has_dropped', '0')
            // todo: confirm if sectionless students (section=null) should be included in CSV
            ->where('faculties.section', '!=', 'null')

            ->join('students', 'users.role_id', '=', 'students.student_number')
            ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')

            ->select(
                'students.student_number',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'faculties.section',
                //'students.has_dropped',
            )
            ->orderBy('students.student_number')
            ->get();
            
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
    */

    public function exportCompanyEvaluations(): BinaryFileResponse
    {
        $shortName = 'company-evaluation';
        $csvFileName = 'company_evaluations.csv';

        $dbTable1 = DB::table('users')
            ->where('role', 'student')
            ->where('forms.short_name', $shortName)
            //->where('form_statuses.status', 'submitted')

            ->join('students', 'users.role_id', '=', 'students.student_number')
            ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')

            ->leftJoin('form_statuses', 'users.id', '=', 'form_statuses.user_id')
            ->leftJoin('forms', 'form_statuses.form_id' ,'=', 'forms.id')
            ->leftJoin('form_answers', 'form_statuses.id', '=', 'form_answers.form_status_id')

            ->leftJoin('rating_scores', 'form_answers.id', '=', 'rating_scores.form_answer_id')
            ->leftJoin('rating_questions', 'rating_scores.rating_question_id', '=', 'rating_questions.id')

            ->select(
                'students.student_number',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'faculties.section',
                'form_statuses.status',
                'rating_questions.id',
                'rating_questions.criterion',
                'rating_scores.score'
            )
            ->orderBy('students.student_number')
            ->get();

        $dbTable2 = DB::table('form_rating_questions')
            ->where('forms.short_name', $shortName)

            ->join('forms', 'form_rating_questions.form_id', '=', 'forms.id')
            ->join('rating_questions', 'form_rating_questions.rating_question_id', '=', 'rating_questions.id')
            
            ->select(
                'rating_questions.id',
                'rating_questions.criterion'
            )
            ->get();
        
        // number of last columns to remove from $dbTable1
        /*
            'rating_questions.id',
            'rating_questions.criterion',
            'rating_scores.score'
        */
        $num_extra_rows = 3;

        // ---

        $csvFile = fopen($csvFileName, 'w');
        
        // get headers of DB query
        $headers = array_keys((array) $dbTable1[0]);
        // remove rating question id, criterion and score from headers
        $reduced_headers = array_slice($headers, 0, count($headers) - $num_extra_rows);
        // get only criterion from [rating_questions.criterion, rating_questions.id]
        $rating_criteria = $dbTable2->pluck('criterion');
        // produce actual headers from reduced headers ++ rating criteria
        $actual_headers = array_merge($reduced_headers, $rating_criteria->toArray());
        fputcsv($csvFile, $actual_headers);

        for ($i = 0; $i < count($dbTable1); $i++) {
            $row = $dbTable1[$i];
            // remove rating question id, criterion and score from row
            $reduced_row = array_slice((array) $row, 0, count((array) $row) - $num_extra_rows);

            // get array of scores
            $rating_scores = [];
            foreach ($dbTable2 as $rating_criterion_id) {
                if ($rating_criterion_id->id == $row->id) array_push($rating_scores, $row->score);
                else array_push($rating_scores, null);
            }

            // if the next row has the same student number (same student, score for next question),
            //      then combine the $rating_scores arrays vertically to get rid of nulls
            /*
                6       null    null    null
                null    6       null    null
                =
                6       6       null    null
            */
            while ($i+1 < count($dbTable1) && $dbTable1[$i+1]->student_number == $row->student_number) {
                $i++;
                $row = $dbTable1[$i];
                
                $current_rating_scores = [];
                foreach ($dbTable2 as $rating_criterion_id) {
                    if ($rating_criterion_id->id == $row->id) array_push($current_rating_scores, $row->score);
                    else array_push($current_rating_scores, null);
                }

                for ($j = 0; $j < count($rating_scores); $j++) {
                    if ($rating_scores[$j] == null) $rating_scores[$j] = $current_rating_scores[$j];
                }
            }

            $actual_row = array_merge($reduced_row, $rating_scores);
            fputcsv($csvFile, (array) $actual_row);
        }

        fclose($csvFile);

        // ---
        
        // todo: show user prompt to download from public folder to actual local filesystem
        return response()->download(public_path($csvFileName));
    }

    /*
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
