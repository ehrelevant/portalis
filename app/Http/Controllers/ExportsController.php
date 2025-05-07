<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportsController extends Controller
{
    public function getExportFilters(Request $request): array
    {
        $filters = $request->validate([
            'year' => ['numeric', 'nullable'],
            'include_enabled' => ['boolean', 'nullable'],
            'include_disabled' => ['boolean', 'nullable'],

            'statuses' => ['array', 'nullable'],

            'include_with_section' => ['boolean', 'nullable'],
            'include_without_section' => ['boolean', 'nullable'],
            'include_drp' => ['boolean', 'nullable'],

            'include_with_company' => ['boolean', 'nullable'],
            'include_without_company' => ['boolean', 'nullable'],
        ]);

        return $filters;
    }

    public function applyGenericExportFilters(array $filters, Builder $dbTableRaw, array $tableNames): Builder
    {
        // for all users/companies
        $year = $filters['year'] ?? null;
        $includeEnabled = $filters['include_enabled'] ?? false;
        $includeDisabled = $filters['include_disabled'] ?? false;

        // ---

        foreach ($tableNames as $tableName) {
            // if year is set, only include queries for given year
            if (!is_null($year))
                $dbTableRaw = $dbTableRaw->where($tableName . '.year', $year);

            // exclude enabled user accounts/companies
            if (!$includeEnabled)
                $dbTableRaw = $dbTableRaw->whereNot($tableName . '.is_disabled', 0);

            // exclude disabled user accounts/companies
            if (!$includeDisabled)
                $dbTableRaw = $dbTableRaw->whereNot($tableName . '.is_disabled', 1);
        }

        return $dbTableRaw;
    }

    public function applySpecificExportFilters(array $filters, Builder $dbTableRaw, bool $hasStatusFilters, bool $hasStudentFilters, bool $hasSupervisorFilters, bool $hasFacultyFilters, bool $hasCompanyFilters): Builder
    {
        // for form statuses
        $statuses = $filters['statuses'] ?? ['For Review', 'Accepted'];

        // for students
        $includeWithSection = $filters['include_with_section'] ?? false;
        $includeWithoutSection = $filters['include_without_section'] ?? false;
        $includeDropped = $filters['include_drp'] ?? false;

        // for supervisors
        $includeWithCompany = $filters['include_with_company'] ?? false;
        $includeWithoutCompany = $filters['include_without_company'] ?? false;

        // ---

        if ($hasStatusFilters) {
            // only include form statuses from array
            $dbTableRaw = $dbTableRaw->whereIn('form_statuses.status', $statuses);
        }

        if ($hasStudentFilters) {
            // exclude students with section
            if (!$includeWithSection)
                $dbTableRaw = $dbTableRaw->where('faculties.section', null);

            // exclude students without section (but didn't drop)
            if (!$includeWithoutSection)
                $dbTableRaw = $dbTableRaw->whereNot(function($query) {
                    $query
                        ->where('faculties.section', null)
                        ->where('students.has_dropped', 0);
                });

            // exclude students who dropped
            if (!$includeDropped)
                $dbTableRaw = $dbTableRaw->whereNot('students.has_dropped', 1);
        }

        if ($hasSupervisorFilters) {
            // exclude supervisors with a company
            if (!$includeWithCompany)
                $dbTableRaw = $dbTableRaw->where('companies.company_name', null);

            // exclude supervisors without a company
            if (!$includeWithoutCompany)
                $dbTableRaw = $dbTableRaw->whereNot('companies.company_name', null);
        }

        if ($hasFacultyFilters) {}

        if ($hasCompanyFilters) {}

        // ---

        return $dbTableRaw;
    }

    public function exportStudentList(Request $request): StreamedResponse
    {
        $csvFileName = 'student_list';

        // ---

        $dbTableRaw = DB::table('users AS users_students')
            ->where('role', 'student')

            ->join('students', 'users_students.role_id', '=', 'students.id')
            ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')

            ->select(
                'users_students.year',
                'students.student_number',
                'users_students.first_name',
                'users_students.middle_name',
                'users_students.last_name',
                'faculties.section',
                'students.has_dropped',
                'users_students.email',
                'students.wordpress_name',
                'students.wordpress_email',
                'users_students.is_disabled AS is_account_disabled',
            )
            ->orderBy('students.student_number');

        // get export filters from request
        $filters = self::getExportFilters($request);
        $tableNames = [
            'users_students',
        ];

        // apply filters to query
        $dbTableRaw = self::applyGenericExportFilters($filters, $dbTableRaw, $tableNames);
        $dbTableRaw = self::applySpecificExportFilters($filters, $dbTableRaw, false, true, false, false, false);
        $dbTable = $dbTableRaw->get();

        // ---

        // store headers of DB query
        if (count($dbTable) > 0)
            // overwrite headers to be safe, though this *should* just be a redundancy
            $headers = array_keys((array) $dbTable[0]);
        else
            $headers = [
                'year',
                'student_number',
                'first_name',
                'middle_name',
                'last_name',
                'section',
                'has_dropped',
                'email',
                'wordpress_name',
                'wordpress_email',
                'is_account_disabled',
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

    public function exportSupervisorList(Request $request): StreamedResponse
    {
        $csvFileName = 'supervisor_list';

        // ---

        $dbTableRaw = DB::table('users AS users_supervisors')
            ->where('role', 'supervisor')

            ->join('supervisors', 'users_supervisors.role_id', '=', 'supervisors.id')
            ->leftJoin('companies', 'supervisors.company_id', '=', 'companies.id')

            ->select(
                'users_supervisors.year',
                'users_supervisors.first_name',
                'users_supervisors.middle_name',
                'users_supervisors.last_name',
                'users_supervisors.email',
                'companies.company_name',
            )
            ->orderBy('users_supervisors.last_name', 'ASC', 'users_supervisors.first_name', 'ASC');

        // get export filters from request
        $filters = self::getExportFilters($request);
        $tableNames = [
            'users_supervisors',
        ];

        // apply filters to query
        $dbTableRaw = self::applyGenericExportFilters($filters, $dbTableRaw, $tableNames);
        $dbTableRaw = self::applySpecificExportFilters($filters, $dbTableRaw, false, false, true, false, false);
        $dbTable = $dbTableRaw->get();

        // ---

        // store headers of DB query
        if (count($dbTable) > 0)
            // overwrite headers to be safe, though this *should* just be a redundancy
            $headers = array_keys((array) $dbTable[0]);
        else
            $headers = [
                'year',
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

    public function exportFacultyList(Request $request): StreamedResponse
    {
        $csvFileName = 'faculty_list';

        // ---

        $dbTableRaw = DB::table('users AS users_faculties')
            ->where('role', 'faculty')

            ->join('faculties', 'users_faculties.role_id', '=', 'faculties.id')

            ->select(
                'users_faculties.year',
                'users_faculties.first_name',
                'users_faculties.middle_name',
                'users_faculties.last_name',
                'users_faculties.email',
                'faculties.section'
            )
            ->orderBy('users_faculties.last_name', 'ASC', 'users_faculties.first_name', 'ASC');

        // get export filters from request
        $filters = self::getExportFilters($request);
        $tableNames = [
            'users_faculties',
        ];

        // apply filters to query
        $dbTableRaw = self::applyGenericExportFilters($filters, $dbTableRaw, $tableNames);
        $dbTableRaw = self::applySpecificExportFilters($filters, $dbTableRaw, false, false, false, true, false);
        $dbTable = $dbTableRaw->get();

        // ---

        // store headers of DB query
        if (count($dbTable) > 0)
            // overwrite headers to be safe, though this *should* just be a redundancy
            $headers = array_keys((array) $dbTable[0]);
        else
            $headers = [
                'year',
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

    public function exportCompanyList(Request $request): StreamedResponse
    {
        $csvFileName = 'company_list';

        // ---

        $dbTableRaw = DB::table('companies')
            ->select(
                'companies.year',
                'companies.company_name',
            )
            ->orderBy('companies.company_name');

        // get export filters from request
        $filters = self::getExportFilters($request);
        $tableNames = [
            'companies',
        ];

        // apply filters to query
        $dbTableRaw = self::applyGenericExportFilters($filters, $dbTableRaw, $tableNames);
        $dbTableRaw = self::applySpecificExportFilters($filters, $dbTableRaw, false, false, false, false, true);
        $dbTable = $dbTableRaw->get();

        // ---

        // store headers of DB query
        $headers = [
            'year',
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

    public function exportFormsAsCsv(Request $request, string $shortName, string $csvFileName, string $formAnswerer, array $tableNames, bool $hasStudentFilters, bool $hasSupervisorFilters, bool $hasFacultyFilters, bool $hasCompanyFilters): StreamedResponse
    {
        // $formAnswerer determines which user table to join with form_statuses, based on what user IDs are expected to be found
        switch ($formAnswerer) {
            case 'student':
                $userTable = 'users_students';
                break;
            case 'supervisor':
                $userTable = 'users_supervisors';
                break;
            case 'faculty':
                $userTable = 'users_faculties';
                break;
            default:
                $userTable = 'users_students';
        }

        // ---

        $dbTable1Raw = DB::table('users AS users_students')
            ->where('users_students.role', 'student')
            ->where('users_supervisors.role', 'supervisor')
            ->where('users_faculties.role', 'faculty')
            ->where('forms.short_name', $shortName)

            ->join('students', 'users_students.role_id', '=', 'students.id')
            ->leftJoin('users AS users_faculties', 'students.faculty_id', '=', 'users_faculties.role_id')
            ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')
            ->leftJoin('users AS users_supervisors', 'students.supervisor_id', '=', 'users_supervisors.role_id')
            ->leftJoin('supervisors', 'students.supervisor_id', '=', 'supervisors.id')
            ->leftJoin('companies', 'supervisors.company_id', '=', 'companies.id')

            ->leftJoin('form_statuses', $userTable . '.id', '=', 'form_statuses.user_id')
            ->leftJoin('forms', 'form_statuses.form_id' ,'=', 'forms.id')
            ->leftJoin('form_answers', 'form_statuses.id', '=', 'form_answers.form_status_id')

            ->leftJoin('rating_scores', 'form_answers.id', '=', 'rating_scores.form_answer_id')
            ->leftJoin('rating_questions', 'rating_scores.rating_question_id', '=', 'rating_questions.id')
            ->leftJoin('open_answers', 'form_answers.id', '=', 'open_answers.form_answer_id')
            ->leftJoin('open_questions', 'open_answers.open_question_id', '=', 'open_questions.id')

            ->select(
                'users_students.year',
                'students.student_number',
                'users_students.first_name',
                'users_students.middle_name',
                'users_students.last_name',
                'faculties.section',
                'students.has_dropped',
                'users_students.is_disabled AS is_student_account_disabled',
                'users_faculties.first_name AS faculty_first_name',
                'users_faculties.middle_name AS faculty_middle_name',
                'users_faculties.last_name AS faculty_last_name',
                'companies.company_name',
                'users_supervisors.first_name AS supervisor_first_name',
                'users_supervisors.middle_name AS supervisor_middle_name',
                'users_supervisors.last_name AS supervisor_last_name',
                'form_statuses.status',
                'rating_questions.id AS rating_question_id',
                'rating_questions.criterion',
                'rating_scores.score',
                'open_questions.id AS open_question_id',
                'open_questions.question',
                'open_answers.answer',
            )
            ->orderBy('students.student_number');

        // get export filters from request
        $filters = self::getExportFilters($request);

        // apply filters to query
        $dbTable1Raw = self::applyGenericExportFilters($filters, $dbTable1Raw, $tableNames);
        $dbTable1Raw = self::applySpecificExportFilters($filters, $dbTable1Raw, true, $hasStudentFilters, $hasSupervisorFilters, $hasFacultyFilters, $hasCompanyFilters);
        $dbTable1 = $dbTable1Raw->get();

        // ---

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
                'rating_question_id',
                'criterion',
                'score',
                'open_question_id',
                'question',
                'answer',
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

        $dbTable3 = DB::table('form_open_questions')
            ->where('forms.short_name', $shortName)

            ->join('forms', 'form_open_questions.form_id', '=', 'forms.id')
            ->join('open_questions', 'form_open_questions.open_question_id', '=', 'open_questions.id')

            ->select(
                'open_questions.id',
                'open_questions.question',
            )
            ->get();

        // number of last columns to remove from $dbTable1
        /*
            'rating_questions.id AS rating_question_id',
            'rating_questions.criterion',
            'rating_scores.score',
            'open_questions.id AS open_question_id',
            'open_questions.question',
            'open_answers.answer'
        */
        $numExtraRows = 6;

        // ---

        $callback = function() use ($headers, $numExtraRows, $dbTable1, $dbTable2, $dbTable3) {
            $csvFile = fopen('php://output', 'w');

            // add header row to CSV
            $reducedHeaders = array_slice($headers, 0, count($headers) - $numExtraRows);                            // remove rating question id, criterion and score from headers
            $ratingCriteria = $dbTable2->pluck('criterion');                                                        // get only criterion from [rating_questions.criterion, rating_questions.id]
            $openQuestions = $dbTable3->pluck('question');                                                          // get only question from [open_questions.question, open_questions.id]
            $actualHeaders = array_merge($reducedHeaders, $ratingCriteria->toArray(), $openQuestions->toArray());   // produce actual headers from reduced headers ++ rating criteria
            fputcsv($csvFile, $actualHeaders);

            // add entry rows to CSV
            for ($i = 0; $i < count($dbTable1); $i++) {
                $row = $dbTable1[$i];

                // remove rating question id, criterion and score from row
                $reducedRow = array_slice((array) $row, 0, count((array) $row) - $numExtraRows);

                // get array of scores
                $ratingScores = [];
                foreach ($dbTable2 as $ratingCriterionId) {
                    if ($ratingCriterionId->id == $row->rating_question_id) array_push($ratingScores, $row->score);
                    else array_push($ratingScores, null);
                }

                $openAnswers = [];
                foreach ($dbTable3 as $openQuestionId) {
                    if ($openQuestionId->id == $row->open_question_id) array_push($openAnswers, $row->answer);
                    else array_push($openAnswers, null);
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
                        if ($ratingCriterionId->id == $row->rating_question_id) array_push($currentRatingScores, $row->score);
                        else array_push($currentRatingScores, null);
                    }

                    for ($j = 0; $j < count($ratingScores); $j++) {
                        if ($ratingScores[$j] == null) $ratingScores[$j] = $currentRatingScores[$j];
                    }

                    $currentOpenAnswers = [];
                    foreach ($dbTable3 as $openQuestionId) {
                        if ($openQuestionId->id == $row->open_question_id) array_push($currentOpenAnswers, $row->answer);
                        else array_push($currentOpenAnswers, null);
                    }

                    for ($j = 0; $j < count($openAnswers); $j++) {
                        if ($openAnswers[$j] == null) $openAnswers[$j] = $currentOpenAnswers[$j];
                    }
                }

                // produce actual row from reduced row ++ rating scores
                $actualRow = array_merge($reducedRow, $ratingScores, $openAnswers);
                fputcsv($csvFile, $actualRow);
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

    public function exportMidsemReports(Request $request): StreamedResponse
    {
        $formAnswerer = 'supervisor';

        $tableNames = [
            'users_students',
        ];

        return self::exportFormsAsCsv($request, 'midsem', 'midsem_reports', $formAnswerer, $tableNames, true, false, false, false);
    }

    public function exportFinalReports(Request $request): StreamedResponse
    {
        $formAnswerer = 'supervisor';

        $tableNames = [
            'users_students',
        ];

        return self::exportFormsAsCsv($request, 'final', 'final_reports', $formAnswerer, $tableNames, true, false, false, false);
    }

    public function exportCompanyEvaluations(Request $request): StreamedResponse
    {
        $formAnswerer = 'student';

        $tableNames = [
            'users_students',
        ];

        return self::exportFormsAsCsv($request, 'company-evaluation', 'company_evaluations', $formAnswerer, $tableNames, true, false, false, false);
    }

    public function exportStudentSelfEvaluations(Request $request): StreamedResponse
    {
        $formAnswerer = 'student';

        $tableNames = [
            'users_students',
        ];

        return self::exportFormsAsCsv($request, 'self-evaluation', 'self_evaluations', $formAnswerer, $tableNames, true, false, false, false);
    }

    public function exportStudentAssessments(Request $request): StreamedResponse
    {
        $formAnswerer = 'supervisor';

        $tableNames = [
            'users_students',
        ];

        return self::exportFormsAsCsv($request, 'intern-evaluation', 'student_assessments', $formAnswerer, $tableNames, true, false, false, false);
    }
}
