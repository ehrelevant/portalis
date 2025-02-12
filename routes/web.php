<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
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

    Route::get('/requirement/{requirement_id}/upload/{student_number?}', [FileSubmissionContoller::class, 'showUploadForm']);
    Route::post('/requirement/{requirement_id}/submit/{student_number?}', [FileSubmissionContoller::class, 'submitDocument']);
    Route::get('/requirement/{requirement_id}/view/{student_number}', [FileSubmissionContoller::class, 'showStudentSubmission']);
    Route::post('/requirement/{requirement_id}/view/{student_number}/validate', [FileSubmissionContoller::class, 'validateStudentSubmission']);
    Route::post('/requirement/{requirement_id}/view/{student_number}/invalidate', [FileSubmissionContoller::class, 'invalidateStudentSubmission']);
    Route::post('/requirement/{requirement_id}/view/{student_number}/reject', [FileSubmissionContoller::class, 'rejectStudentSubmission']);

    // Form Answering/Viewing
    Route::get('/form/{short_name}/answer/{role_id?}', [FormController::class, 'answerForm']);
    Route::post('/form/{short_name}/draft/{role_id?}', [FormController::class, 'draftForm']);
    Route::post('/form/{short_name}/submit/{role_id?}', [FormController::class, 'submitForm']);
    Route::get('/form/{short_name}/view/{role_id}', [FormController::class, 'viewForm'])->middleware([EnsureUserHasRole::class . ':faculty,admin']);

    // Form Validation
    Route::post('/form/{short_name}/validate/{user_id}', [FormController::class, 'validateForm']);
    Route::post('/form/{short_name}/invalidate/{user_id}', [FormController::class, 'invalidateForm']);
    Route::post('/form/{short_name}/reject/{user_id}', [FormController::class, 'rejectForm']);

    Route::put('/dashboard/students/{student_number}/assign/section', [FacultyController::class, 'assignStudentSection']);
    Route::put('/dashboard/students/{student_number}/assign/section/{new_section}', [FacultyController::class, 'assignStudentSection']);

    Route::middleware([EnsureUserHasRole::class . ':faculty'])->group(function () {
        Route::get('/dashboard/students', [FacultyController::class, 'showStudents']);

        Route::get('/dashboard/students/{student_number}', [FacultyController::class, 'showStudent']);
        Route::put('/dashboard/update-deadlines', [FacultyController::class, 'updateDeadlines']);

        Route::get('/dashboard/supervisors', [FacultyController::class, 'showSupervisors']);

        Route::post('/import/students', [FacultyController::class, 'importStudents']);
        Route::post('/import/supervisors', [FacultyController::class, 'importSupervisors']);

        Route::get('/export/students/sections', [FacultyController::class, 'exportStudentSections']);
        Route::get('/export/students/midsem-reports', [FacultyController::class, 'exportMidsemReportStudents']);
        Route::get('/export/students/final-reports', [FacultyController::class, 'exportFinalReportStudents']);
        Route::get('/export/students/company-evaluations', [FacultyController::class, 'exportCompanyEvaluations']);
        Route::get('/export/students/student-assessments', [FacultyController::class, 'exportStudentAssessments']);
        Route::get('/export/supervisors/midsem-reports', [FacultyController::class, 'exportMidsemReportSupervisors']);
        Route::get('/export/supervisors/final-reports', [FacultyController::class, 'exportFinalReportSupervisors']);
    });

    Route::middleware([EnsureUserHasRole::class . ':admin'])->group(function () {
        Route::get('/dashboard/admin/students', [AdminController::class, 'showStudents']);
        Route::get('/dashboard/admin/supervisors', [AdminController::class, 'showSupervisors']);
        Route::get('/dashboard/admin/companies', [AdminController::class, 'showCompanies']);
        Route::get('/dashboard/admin/faculties', [AdminController::class, 'showFaculties']);

        Route::post('/add/student', [AdminController::class, 'addStudent']);
        Route::post('/add/supervisor', [AdminController::class, 'addSupervisor']);
        Route::post('/add/company', [AdminController::class, 'addCompany']);
        Route::post('/add/faculty', [AdminController::class, 'addFaculty']);

        Route::delete('/delete/student', [AdminController::class, 'deleteStudent']);
        Route::delete('/delete/supervisor', [AdminController::class, 'deleteSupervisor']);
        Route::delete('/delete/company', [AdminController::class, 'deleteCompany']);
        Route::delete('/delete/faculty', [AdminController::class, 'deleteFaculty']);
    });

    Route::put('/globals/update-website-state', [WebsiteStateController::class, 'updateWebsiteState']);
    Route::get('/file/submission/{student_number}/{requirement_id}', [FileSubmissionContoller::class, 'showStudentDocument']);

    Route::post('/logout', [LoginController::class, 'logout']);
});
