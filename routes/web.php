<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\FileSubmissionContoller;
use App\Http\Controllers\FormController;
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
        Route::get('/requirement/{requirement_id}/upload', [StudentController::class, 'showUploadDocument']);
        Route::post('/requirement/{requirement_id}/submit', [StudentController::class, 'submitDocument']);
    });

    // Form Answering
    Route::middleware([EnsureUserHasRole::class . ':student'])->group(function () {
        Route::get('/form/company-evaluation/answer', [FormController::class, 'answerCompanyEvaluationForm']);
        Route::post('/form/company-evaluation/draft', [FormController::class, 'draftCompanyEvaluationForm']);
        Route::post('/form/company-evaluation/submit', [FormController::class, 'submitCompanyEvaluationForm']);

        Route::get('/form/self-evaluation/answer', [FormController::class, 'answerSelfEvaluationForm']);
        Route::post('/form/self-evaluation/draft', [FormController::class, 'draftSelfEvaluationForm']);
        Route::post('/form/self-evaluation/submit', [FormController::class, 'submitSelfEvaluationForm']);
    });

    Route::middleware([EnsureUserHasRole::class . ':supervisor'])->group(function () {
        Route::get('/form/{short_name}/answer', [FormController::class, 'answerReportForm'])->whereIn('short_name', ['midsem', 'final']);;
        Route::post('/form/{short_name}/draft', [FormController::class, 'draftReportForm'])->whereIn('short_name', ['midsem', 'final']);;
        Route::post('/form/{short_name}/submit', [FormController::class, 'submitReportForm'])->whereIn('short_name', ['midsem', 'final']);;

        Route::get('/form/intern-evaluation/answer', [FormController::class, 'answerInternEvaluationForm']);
        Route::post('/form/intern-evaluation/draft', [FormController::class, 'draftInternEvaluationForm']);
        Route::post('/form/intern-evaluation/submit', [FormController::class, 'submitInternEvaluationForm']);
    });

    // Form Viewing
    Route::get('/form/{short_name}/view/{supervisor_id}', [FormController::class, 'viewReportForm'])->whereIn('short_name', ['midsem', 'final']);
    Route::get('/form/company-evaluation/view/{student_number}', [FormController::class, 'viewCompanyEvaluationForm']);
    Route::get('/form/intern-evaluation/view/{supervisor_id}', [FormController::class, 'viewInternEvaluationForm']);
    Route::get('/form/self-evaluation/view/{student_number}', [FormController::class, 'viewSelfEvaluationForm']);

    // Form Validation
    Route::post('/form/{short_name}/validate/{user_id}', [FormController::class, 'validateForm']);
    Route::post('/form/{short_name}/invalidate/{user_id}', [FormController::class, 'invalidateForm']);
    Route::post('/form/{short_name}/reject/{user_id}', [FormController::class, 'rejectForm']);

    Route::middleware([EnsureUserHasRole::class . ':faculty'])->group(function () {
        Route::get('/dashboard/students', [FacultyController::class, 'showStudents']);

        Route::get('/dashboard/students/{student_number}', [FacultyController::class, 'showStudent']);
        Route::get('/dashboard/students/{student_number}/{requirement_id}', [FileSubmissionContoller::class, 'showStudentSubmission']);
        Route::post('/dashboard/students/{student_number}/{requirement_id}/validate', [FileSubmissionContoller::class, 'validateStudentSubmission']);
        Route::post('/dashboard/students/{student_number}/{requirement_id}/invalidate', [FileSubmissionContoller::class, 'invalidateStudentSubmission']);
        Route::post('/dashboard/students/{student_number}/{requirement_id}/reject', [FileSubmissionContoller::class, 'rejectStudentSubmission']);
        Route::put('/dashboard/students/{student_number}/assign/section', [FacultyController::class, 'assignStudentSection']);
        Route::put('/dashboard/students/{student_number}/assign/section/{new_section}', [FacultyController::class, 'assignStudentSection']);
        Route::put('/dashboard/update-deadlines', [FacultyController::class, 'updateDeadlines']);

        Route::get('/dashboard/supervisors', [FacultyController::class, 'showSupervisors']);

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
