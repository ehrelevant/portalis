<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportsController;
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

    Route::middleware(['throttle:pin_email'])->group(function () {
        Route::post('/login/send-pin', [LoginController::class, 'sendPin']);
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/account', [AccountController::class, 'show']);
    Route::get('/dashboard', [DashboardController::class, 'show']);

    // Requirement Upload/Submission (Role checking is done in function)
    Route::get('/requirement/{requirement_id}/upload/{student_number?}', [FileSubmissionContoller::class, 'showUploadForm']);
    Route::post('/requirement/{requirement_id}/submit/{student_number?}', [FileSubmissionContoller::class, 'submitDocument']);

    // Form Answering/Viewing (Role checking is done in function)
    Route::get('/form/{short_name}/answer/{role_id?}', [FormController::class, 'answerForm']);
    Route::post('/form/{short_name}/draft/{role_id?}', [FormController::class, 'draftForm']);
    Route::post('/form/{short_name}/submit/{role_id?}', [FormController::class, 'submitForm']);
    Route::get('/form/{short_name}/view/{role_id}', [FormController::class, 'viewForm'])->middleware([EnsureUserHasRole::class . ':faculty,admin']);

    // Form Validation
    Route::post('/form/{short_name}/validate/{user_id}', [FormController::class, 'validateForm']);
    Route::post('/form/{short_name}/invalidate/{user_id}', [FormController::class, 'invalidateForm']);
    Route::post('/form/{short_name}/reject/{user_id}', [FormController::class, 'rejectForm']);

    Route::middleware([EnsureUserHasRole::class . ':faculty,admin'])->group(function () {
        Route::put('/students/{student_number}/assign/section/{new_section?}', [FacultyController::class, 'assignStudentSection']);
        Route::put('/students/{student_number}/assign/supervisor/{supervisor_id?}', [FacultyController::class, 'assignStudentSupervisor']);
        Route::put('/supervisors/{supervisor_id}/assign/company/{company_id?}', [FacultyController::class, 'assignSupervisorCompany']);

        Route::post('/import/students', [FacultyController::class, 'importStudents']);
        Route::post('/import/supervisors', [FacultyController::class, 'importSupervisors']);

        Route::get('/export/students/sections', [ExportsController::class, 'exportStudentSections']);
        Route::get('/export/students/midsem-reports', [ExportsController::class, 'exportMidsemReportStudents']);
        Route::get('/export/students/final-reports', [ExportsController::class, 'exportFinalReportStudents']);
        Route::get('/export/students/company-evaluations', [ExportsController::class, 'exportCompanyEvaluations']);
        Route::get('/export/students/student-assessments', [ExportsController::class, 'exportStudentAssessments']);
        Route::get('/export/supervisors/midsem-reports', [ExportsController::class, 'exportMidsemReportSupervisors']);
        Route::get('/export/supervisors/final-reports', [ExportsController::class, 'exportFinalReportSupervisors']);

        // Requirement Viewing
        Route::get('/requirement/{requirement_id}/view/{student_number}', [FileSubmissionContoller::class, 'showStudentSubmission']);

        // Requirement Validation
        Route::post('/requirement/{requirement_id}/view/{student_number}/validate', [FileSubmissionContoller::class, 'validateStudentSubmission']);
        Route::post('/requirement/{requirement_id}/view/{student_number}/invalidate', [FileSubmissionContoller::class, 'invalidateStudentSubmission']);
        Route::post('/requirement/{requirement_id}/view/{student_number}/reject', [FileSubmissionContoller::class, 'rejectStudentSubmission']);

        // Update Settings
        Route::put('/globals/settings/update', [WebsiteStateController::class, 'updateSettings']);
    });

    Route::middleware([EnsureUserHasRole::class . ':faculty'])->group(function () {
        Route::get('/dashboard/students', [FacultyController::class, 'showStudents']);
        Route::get('/dashboard/supervisors', [FacultyController::class, 'showSupervisors']);
        Route::get('/dashboard/companies', [FacultyController::class, 'showCompanies']);
    });

    Route::middleware([EnsureUserHasRole::class . ':admin'])->group(function () {
        Route::get('/dashboard/admin/students', [AdminController::class, 'showStudents']);
        Route::get('/dashboard/admin/supervisors', [AdminController::class, 'showSupervisors']);
        Route::get('/dashboard/admin/companies', [AdminController::class, 'showCompanies']);
        Route::get('/dashboard/admin/faculties', [AdminController::class, 'showFaculties']);

        Route::post('/dashboard/admin/students/add', [AdminController::class, 'addStudent']);
        Route::post('/dashboard/admin/supervisors/add', [AdminController::class, 'addSupervisor']);
        Route::post('/dashboard/admin/companies/add', [AdminController::class, 'addCompany']);
        Route::post('/dashboard/admin/faculties/add', [AdminController::class, 'addFaculty']);

        Route::delete('/dashboard/admin/students/delete/{student_number}', [AdminController::class, 'deleteStudent']);
        Route::delete('/dashboard/admin/supervisors/delete/{supervisor_id}', [AdminController::class, 'deleteSupervisor']);
        Route::delete('/dashboard/admin/companies/delete/{company_id}', [AdminController::class, 'deleteCompany']);
        Route::delete('/dashboard/admin/faculties/delete/{faculty_id}', [AdminController::class, 'deleteFaculty']);
    });

    // View submitted file (Role checking is done in function)
    Route::get('/file/submission/{student_number}/{requirement_id}', [FileSubmissionContoller::class, 'showStudentDocument']);

    Route::post('/logout', [LoginController::class, 'logout']);
});
