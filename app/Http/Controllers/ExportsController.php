<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportsController extends Controller
{
    public function exportStudentSections(): StreamedResponse
    {
        $csvFileName = 'student_sections';

        $dbTable = DB::table('users')
            ->where('role', 'student')
            // todo: confirm if dropped students (section="DRP") should be included in CSV
            ->where('students.has_dropped', '0')
            // todo: confirm if sectionless students (section=null) should be included in CSV
            ->where('faculties.section', '!=', 'null')

            ->join('students', 'users.role_id', '=', 'students.id')
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

        // store headers of DB query
        if (count($dbTable) > 0)
            // overwrite headers to be safe, though this *should* just be a redundancy
            $headers = array_keys((array) $dbTable[0]);
        else
            $headers = [
                'student_number',
                'first_name',
                'middle_name',
                'last_name',
                'section',
                //'has_dropped',
            ];

        // ---

        $callback = function() use ($headers, $dbTable) {
            $csvFile = fopen('php://output', 'w');

            // add header row to CSV
            fputcsv($csvFile, $headers);

            // add entry rows to CSV
            foreach ($dbTable as $row) {
                fputcsv($csvFile, (array) $row);
            }

            fclose($csvFile);
        };

        // store headers of DB query
        if (count($dbTable) > 0)
            // overwrite headers to be safe, though this *should* just be a redundancy
            $headers = array_keys((array) $dbTable[0]);
        else
            $headers = [
                'student_number',
                'first_name',
                'middle_name',
                'last_name',
                'section',
                //'has_dropped',
            ];

        // ---

        $content_headers = [
            'Cache-Control'         => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'          => 'text/csv',
            'Content-Disposition'   => 'attachment; filename=' . $csvFileName . '.csv',
            'Expires'               => '0',
            'Pragma'                => 'public'
        ];

        return response()->stream($callback, 200, $content_headers);
    }

    public function exportSupervisorList(): StreamedResponse
    {
        $csvFileName = 'supervisor_list';

        $dbTable = DB::table('users')
            ->where('role', 'supervisor')

            ->join('supervisors', 'users.role_id', '=', 'supervisors.id')
            ->leftJoin('companies', 'supervisors.company_id', '=', 'companies.id')

            ->select(
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'companies.company_name',
            )
            ->orderBy('users.last_name', 'ASC', 'users.first_name', 'ASC')
            ->get();

        // store headers of DB query
        if (count($dbTable) > 0)
            // overwrite headers to be safe, though this *should* just be a redundancy
            $headers = array_keys((array) $dbTable[0]);
        else
            $headers = [
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'company_name',
            ];

        // ---

        $callback = function() use ($headers, $dbTable) {
            $csvFile = fopen('php://output', 'w');

            // add header row to CSV
            fputcsv($csvFile, $headers);

            // add entry rows to CSV
            foreach ($dbTable as $row) {
                fputcsv($csvFile, (array) $row);
            }

            fclose($csvFile);
        };

        // ---

        $content_headers = [
            'Cache-Control'         => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'          => 'text/csv',
            'Content-Disposition'   => 'attachment; filename=' . $csvFileName . '.csv',
            'Expires'               => '0',
            'Pragma'                => 'public'
        ];

        return response()->stream($callback, 200, $content_headers);
    }

    public function exportFacultyList(): StreamedResponse
    {
        $csvFileName = 'faculty_list';

        $dbTable = DB::table('users')
            ->where('role', 'faculty')

            ->join('faculties', 'users.role_id', '=', 'faculties.id')

            ->select(
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'faculties.section'
            )
            ->orderBy('users.last_name', 'ASC', 'users.first_name', 'ASC')
            ->get();

        // store headers of DB query
        if (count($dbTable) > 0)
            // overwrite headers to be safe, though this *should* just be a redundancy
            $headers = array_keys((array) $dbTable[0]);
        else
            $headers = [
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'section',
            ];

        // ---

        $callback = function() use ($headers, $dbTable) {
            $csvFile = fopen('php://output', 'w');

            // add header row to CSV
            fputcsv($csvFile, $headers);

            // add entry rows to CSV
            foreach ($dbTable as $row) {
                fputcsv($csvFile, (array) $row);
            }

            fclose($csvFile);
        };

        // ---

        $content_headers = [
            'Cache-Control'         => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'          => 'text/csv',
            'Content-Disposition'   => 'attachment; filename=' . $csvFileName . '.csv',
            'Expires'               => '0',
            'Pragma'                => 'public'
        ];

        return response()->stream($callback, 200, $content_headers);
    }

    public function exportCompanyList(): StreamedResponse
    {
        $csvFileName = 'company_list';

        $dbTable = DB::table('companies')
            ->select(
                'companies.company_name',
            )
            ->orderBy('companies.company_name')
            ->get();

        // store headers of DB query
        $headers = [
            'company_name',
        ];

        // ---

        $callback = function() use ($headers, $dbTable) {
            $csvFile = fopen('php://output', 'w');

            // add header row to CSV
            fputcsv($csvFile, $headers);

            // add entry rows to CSV
            foreach ($dbTable as $row) {
                fputcsv($csvFile, (array) $row);
            }

            fclose($csvFile);
        };

        // ---

        $content_headers = [
            'Cache-Control'         => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'          => 'text/csv',
            'Content-Disposition'   => 'attachment; filename=' . $csvFileName . '.csv',
            'Expires'               => '0',
            'Pragma'                => 'public'
        ];

        return response()->stream($callback, 200, $content_headers);
    }

    // ---

    public function exportFormsAsCsv(string $shortName, string $csvFileName): StreamedResponse
    {
        $dbTable1 = DB::table('users')
            ->where('role', 'student')
            ->where('forms.short_name', $shortName)
            //->where('form_statuses.status', 'For Review')

            ->join('students', 'users.role_id', '=', 'students.id')
            ->join('faculties', 'students.faculty_id', '=', 'faculties.id')
            ->whereNot('students.has_dropped', true)

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

        // store headers of DB query
        if (count($dbTable1) > 0)
            // overwrite headers to be safe, though this *should* just be a redundancy
            $headers = array_keys((array) $dbTable1[0]);
        else
            $headers = [
                'student_number',
                'first_name',
                'middle_name',
                'last_name',
                'section',
                'status',
                'id',
                'criterion',
                'score'
            ];

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
        $numExtraRows = 3;

        // ---

        $callback = function() use ($headers, $numExtraRows, $dbTable1, $dbTable2) {
            $csvFile = fopen('php://output', 'w');

        // add header row to CSV
        $reducedHeaders = array_slice($headers, 0, count($headers) - $numExtraRows);    // remove rating question id, criterion and score from headers
        $ratingCriteria = $dbTable2->pluck('criterion');                                // get only criterion from [rating_questions.criterion, rating_questions.id]
        $actualHeaders = array_merge($reducedHeaders, $ratingCriteria->toArray());      // produce actual headers from reduced headers ++ rating criteria
        fputcsv($csvFile, $actualHeaders);

        // add entry rows to CSV
        for ($i = 0; $i < count($dbTable1); $i++) {
            $row = $dbTable1[$i];

            // remove rating question id, criterion and score from row
            $reducedRow = array_slice((array) $row, 0, count((array) $row) - $numExtraRows);

            // get array of scores
            $ratingScores = [];
            foreach ($dbTable2 as $ratingCriterionId) {
                if ($ratingCriterionId->id == $row->id) array_push($ratingScores, $row->score);
                else array_push($ratingScores, null);
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

                $currentRatingScores = [];
                foreach ($dbTable2 as $ratingCriterionId) {
                    if ($ratingCriterionId->id == $row->id) array_push($currentRatingScores, $row->score);
                    else array_push($currentRatingScores, null);
                }

                for ($j = 0; $j < count($ratingScores); $j++) {
                    if ($ratingScores[$j] == null) $ratingScores[$j] = $currentRatingScores[$j];
                }
            }

            // produce actual row from reduced row ++ rating scores
            $actualRow = array_merge($reducedRow, $ratingScores);
            fputcsv($csvFile, (array) $actualRow);
        }

        fclose($csvFile);
    };

        // ---

        $content_headers = [
            'Cache-Control'         => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'          => 'text/csv',
            'Content-Disposition'   => 'attachment; filename=' . $csvFileName . '.csv',
            'Expires'               => '0',
            'Pragma'                => 'public'
        ];

        return response()->stream($callback, 200, $content_headers);
    }

    /*
    public function exportMidsemReportStudents(): BinaryFileResponse
    {

    }

    public function exportFinalReportStudents(): BinaryFileResponse
    {

    }
    */

    public function exportCompanyEvaluations(): StreamedResponse
    {
        return self::exportFormsAsCsv('company-evaluation', 'company_evaluations');
    }

    public function exportStudentAssessments(): StreamedResponse
    {
        return self::exportFormsAsCsv('self-evaluation', 'student_assessments');
    }

    /*
    public function exportMidsemReportSupervisors(): BinaryFileResponse
    {

    }

    public function exportFinalReportSupervisors(): BinaryFileResponse
    {

    }
    */
}
