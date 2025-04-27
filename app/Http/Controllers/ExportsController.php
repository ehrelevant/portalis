<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportsController extends Controller
{
    public function exportStudentList(): StreamedResponse
    {
        $csvFileName = 'student_list';

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
                'users.email',
                'students.wordpress_name',
                'students.wordpress_email',
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
                'email',
                'wordpress_name',
                'wordpress_email'
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

    public function exportFormsAsCsv(string $shortName, string $csvFileName, ?int $year = null, array $statuses = ['For Review', 'Accepted'], bool $includeEnabled = true, bool $includeDisabled = false, bool $includeWithSection = true, bool $includeWithoutSection = true, bool $includeDropped = false): StreamedResponse
    {
        if (empty($statuses)) {
            // todo: redirect with error?
        }

        if (!$includeEnabled && !$includeDisabled) {
            // todo: redirect with error?
        }

        if (!$includeWithSection && !$includeWithoutSection && !$includeDropped) {
            // todo: redirect with error?
        }

        // ---

        $dbTable1Raw = DB::table('users AS users_students')
            ->where('users_students.role', 'student')
            ->where('forms.short_name', $shortName)

            ->join('students', 'users_students.role_id', '=', 'students.id')
            ->leftJoin('users AS users_faculties', 'students.faculty_id', '=', 'users_faculties.role_id')
            ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')
            ->leftJoin('users AS users_supervisors', 'students.supervisor_id', '=', 'users_supervisors.role_id')
            ->leftJoin('supervisors', 'students.supervisor_id', '=', 'supervisors.id')
            ->leftJoin('companies', 'supervisors.company_id', '=', 'companies.id')

            ->leftJoin('form_statuses', 'users_students.id', '=', 'form_statuses.user_id')
            ->leftJoin('forms', 'form_statuses.form_id' ,'=', 'forms.id')
            ->leftJoin('form_answers', 'form_statuses.id', '=', 'form_answers.form_status_id')

            ->leftJoin('rating_scores', 'form_answers.id', '=', 'rating_scores.form_answer_id')
            ->leftJoin('rating_questions', 'rating_scores.rating_question_id', '=', 'rating_questions.id')

            ->select(
                'users_students.year',
                'students.student_number',
                'users_students.first_name',
                'users_students.middle_name',
                'users_students.last_name',
                'faculties.section',
                'students.has_dropped',
                'users_faculties.first_name AS faculty_first_name',
                'users_faculties.middle_name AS faculty_middle_name',
                'users_faculties.last_name AS faculty_last_name',
                'companies.company_name',
                'users_supervisors.first_name AS supervisor_first_name',
                'users_supervisors.middle_name AS supervisor_middle_name',
                'users_supervisors.last_name AS supervisor_last_name',
                'form_statuses.status',
                'rating_questions.id',
                'rating_questions.criterion',
                'rating_scores.score'
            )
            ->orderBy('students.student_number');

        // ---

        // if year is set, only include queries for given year
        if (!is_null($year))
            $dbTable1Raw = $dbTable1Raw->where('users_students.year', $year);

        // ---

        // only include form statuses from array
        $dbTable1Raw = $dbTable1Raw->whereIn('form_statuses.status', $statuses);

        // ---

        // exclude enabled student accounts
        if (!$includeEnabled)
            $dbTable1Raw = $dbTable1Raw->whereNot('students.has_dropped', 0);

        // exclude disabled student accounts
        if (!$includeDisabled)
            $dbTable1Raw = $dbTable1Raw->whereNot('students.has_dropped', 1);

        // ---

        // exclude students with section
        if (!$includeWithSection)
            $dbTable1Raw = $dbTable1Raw->where('faculties.section', null);

        // exclude students without section (but didn't drop)
        if (!$includeWithoutSection)
            $dbTable1Raw = $dbTable1Raw->whereNot(function($query) {
                $query
                    ->where('faculties.section', null)
                    ->where('students.has_dropped', 0);
            });

        // exclude students who dropped
        if (!$includeDropped)
            $dbTable1Raw = $dbTable1Raw->where('students.has_dropped', 0);

        // ---

        $dbTable1 = $dbTable1Raw->get();

        // store headers of DB query
        if (count($dbTable1) > 0)
            // overwrite headers to be safe, though this *should* just be a redundancy
            $headers = array_keys((array) $dbTable1[0]);
        else
            $headers = [
                'year',
                'student_number',
                'first_name',
                'middle_name',
                'last_name',
                'section',
                'has_dropped',
                'faculty_first_name',
                'faculty_middle_name',
                'faculty_last_name',
                'company_name',
                'supervisor_first_name',
                'supervisor_middle_name',
                'supervisor_last_name',
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

    public function exportMidsemReports(): StreamedResponse
    {
        return self::exportFormsAsCsv('midsem', 'midsem_reports');
    }

    public function exportFinalReports(): StreamedResponse
    {
        return self::exportFormsAsCsv('final', 'final_reports');
    }

    public function exportCompanyEvaluations(): StreamedResponse
    {
        return self::exportFormsAsCsv('company-evaluation', 'company_evaluations');
    }

    public function exportStudentAssessments(): StreamedResponse
    {
        return self::exportFormsAsCsv('self-evaluation', 'student_assessments');
    }
}
