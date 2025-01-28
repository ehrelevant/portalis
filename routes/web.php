<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\FileSubmissionContoller;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\WebsiteStateController;
use App\Http\Middleware\EnsureCorrectPhase;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('home/Index');
})->name('home');

Route::get('/privacy', function () {
    return Inertia::render('privacy/Index');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', function () {
        return Inertia::render('login/Index');
    });

    Route::post('/login', [LoginController::class, 'authenticate']);
    Route::post('/login/send_pin', [LoginController::class, 'sendPin']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/account', [AccountController::class, 'show']);
    Route::get('/dashboard', [DashboardController::class, 'show']);

    Route::middleware([EnsureUserHasRole::class . ':student'])->group(function () {
        Route::get('/dashboard/requirement/{requirement_id}/upload', [StudentController::class, 'showUploadDocument']);
        Route::post('/dashboard/requirement/{requirement_id}/submit', [StudentController::class, 'submitDocument']);
    });

    Route::middleware([EnsureUserHasRole::class . ':supervisor'])->group(function () {
        Route::get('/dashboard/report/midsem', [SupervisorController::class, 'showMidsemReport']);
        Route::put('/dashboard/report/midsem/draft', [SupervisorController::class, 'draftMidsemReport']);
        Route::put('/dashboard/report/midsem/submit', [SupervisorController::class, 'submitMidsemReport']);

        Route::get('/dashboard/report/final', [SupervisorController::class, 'showFinalReport']);
        Route::post('/dashboard/report/final/draft', [SupervisorController::class, 'draftFinalReport']);
        Route::post('/dashboard/report/final/submit', [SupervisorController::class, 'submitFinalReport']);
    });

    Route::middleware([EnsureUserHasRole::class . ':faculty'])->group(function () {
        Route::get('/dashboard/students', [FacultyController::class, 'showStudents']);

        Route::get('/dashboard/students/{student_number}', [FacultyController::class, 'showStudent']);
        Route::get('/dashboard/students/{student_number}/{requirement_id}', [FileSubmissionContoller::class, 'showStudentSubmission']);
        Route::post('/dashboard/students/{student_number}/{requirement_id}/validate', [FileSubmissionContoller::class, 'validateStudentSubmission']);
        Route::post('/dashboard/students/{student_number}/{requirement_id}/invalidate', [FileSubmissionContoller::class, 'invalidateStudentSubmission']);
        Route::post('/dashboard/students/{student_number}/{requirement_id}/reject', [FileSubmissionContoller::class, 'rejectStudentSubmission']);
        Route::put('/dashboard/students/{student_number}/assign/section', [FacultyController::class, 'assignStudentSection']);
        Route::put('/dashboard/students/{student_number}/assign/section/{new_section}', [FacultyController::class, 'assignStudentSection']);
        Route::put('/dashboard/students/update-deadlines', [FacultyController::class, 'updateRequirementDeadlines']);

        Route::get('/dashboard/supervisors', [FacultyController::class, 'showSupervisors']);

        Route::get('/dashboard/supervisors/{supervisor_id}/midsem', [FormController::class, 'showMidsemReport']);
        Route::post('/dashboard/supervisors/{supervisor_id}/midsem/validate', [FormController::class, 'validateMidsemReport']);
        Route::post('/dashboard/supervisors/{supervisor_id}/midsem/invalidate', [FormController::class, 'invalidateMidsemReport']);
        Route::post('/dashboard/supervisors/{supervisor_id}/midsem/reject', [FormController::class, 'rejectMidsemReport']);

        Route::get('/dashboard/supervisors/{supervisor_id}/final', [FormController::class, 'showFinalReport']);
        Route::post('/dashboard/supervisors/{supervisor_id}/final/validate', [FormController::class, 'validateFinalReport']);
        Route::post('/dashboard/supervisors/{supervisor_id}/final/invalidate', [FormController::class, 'invalidateFinalReport']);
        Route::post('/dashboard/supervisors/{supervisor_id}/final/reject', [FormController::class, 'rejectFinalReport']);

        Route::get('/dashboard/companies', [FacultyController::class, 'showCompanies']);
        Route::get('/dashboard/companies/{company_id}', [FacultyController::class, 'showCompanies']);

        Route::post('/dashboard/import/students', [FacultyController::class, 'importStudents']);
        Route::post('/dashboard/import/supervisors', [FacultyController::class, 'importSupervisors']);

        Route::get('/dashboard/export/students/sections', [FacultyController::class, 'exportStudentSections']);
        Route::get('/dashboard/export/students/midsem-reports', [FacultyController::class, 'exportMidsemReportStudents']);
        Route::get('/dashboard/export/students/final-reports', [FacultyController::class, 'exportFinalReportStudents']);
        Route::get('/dashboard/export/students/company-evaluations', [FacultyController::class, 'exportCompanyEvaluations']);
        Route::get('/dashboard/export/students/student-assessments', [FacultyController::class, 'exportStudentAssessments']);
        Route::get('/dashboard/export/supervisors/midsem-reports', [FacultyController::class, 'exportMidsemReportSupervisors']);
        Route::get('/dashboard/export/supervisors/final-reports', [FacultyController::class, 'exportFinalReportSupervisors']);


        Route::put('/globals/update-website-state', [WebsiteStateController::class, 'updateWebsiteState']);
    });

    Route::get('/file/submission/{student_number}/{requirement_id}', [FileSubmissionContoller::class, 'showStudentDocument']);

    Route::post('/logout', [LoginController::class, 'logout']);
});
