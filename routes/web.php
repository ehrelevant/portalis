<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportsController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\FileSubmissionContoller;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ImportsController;
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
    Route::get('/requirement/{requirement_id}/upload/{student_id?}', [FileSubmissionContoller::class, 'showUploadForm']);
    Route::post('/requirement/{requirement_id}/submit/{student_id?}', [FileSubmissionContoller::class, 'submitDocument']);

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
        Route::put('/students/{student_id}/assign/section/{new_section?}', [FacultyController::class, 'assignStudentSection']);
        Route::put('/students/{student_id}/assign/supervisor/{supervisor_id?}', [FacultyController::class, 'assignStudentSupervisor']);
        Route::put('/supervisors/{supervisor_id}/assign/company/{company_id?}', [FacultyController::class, 'assignSupervisorCompany']);

        Route::get('/list/students/upload', [ImportsController::class, 'showStudentCsvUploadForm']);
        Route::get('/list/supervisors/upload', [ImportsController::class, 'showSupervisorCsvUploadForm']);
        Route::get('/list/faculties/upload', [ImportsController::class, 'showFacultyCsvUploadForm']);
        Route::get('/list/companies/upload', [ImportsController::class, 'showCompanyCsvUploadForm']);
        Route::post('/list/students/submit', [ImportsController::class, 'submitStudentCsv']);
        Route::post('/list/supervisors/submit', [ImportsController::class, 'submitSupervisorCsv']);
        Route::post('/list/faculties/submit', [ImportsController::class, 'submitFacultyCsv']);
        Route::post('/list/companies/submit', [ImportsController::class, 'submitCompanyCsv']);

        Route::post('/import/students', [FacultyController::class, 'importStudents']);
        Route::post('/import/supervisors', [FacultyController::class, 'importSupervisors']);

        Route::get('/export/students/sections', [ExportsController::class, 'exportStudentSections']);
        Route::get('/export/students/midsem-reports', [ExportsController::class, 'exportMidsemReportStudents']);
        Route::get('/export/students/final-reports', [ExportsController::class, 'exportFinalReportStudents']);
        Route::get('/export/students/company-evaluations', [ExportsController::class, 'exportCompanyEvaluations']);
        Route::get('/export/students/student-assessments', [ExportsController::class, 'exportStudentAssessments']);
        Route::get('/export/supervisors/list', [ExportsController::class, 'exportSupervisorList']);
        Route::get('/export/supervisors/midsem-reports', [ExportsController::class, 'exportMidsemReportSupervisors']);
        Route::get('/export/supervisors/final-reports', [ExportsController::class, 'exportFinalReportSupervisors']);
        Route::get('/export/faculties/list', [ExportsController::class, 'exportFacultyList']);
        Route::get('/export/companies/list', [ExportsController::class, 'exportCompanyList']);

        // Requirement Viewing
        Route::get('/requirement/{requirement_id}/view/{student_id}', [FileSubmissionContoller::class, 'showStudentSubmission']);

        // Requirement Validation
        Route::post('/requirement/{requirement_id}/view/{student_id}/validate', [FileSubmissionContoller::class, 'validateStudentSubmission']);
        Route::post('/requirement/{requirement_id}/view/{student_id}/invalidate', [FileSubmissionContoller::class, 'invalidateStudentSubmission']);
        Route::post('/requirement/{requirement_id}/view/{student_id}/reject', [FileSubmissionContoller::class, 'rejectStudentSubmission']);

        // Update Settings
        Route::put('/globals/settings/update', [WebsiteStateController::class, 'updateSettings']);

        // Add/Update/Delete Routes
        Route::post('/api/add/student', [AdminController::class, 'addStudent']);
        Route::post('/api/add/supervisor', [AdminController::class, 'addSupervisor']);
        Route::post('/api/add/company', [AdminController::class, 'addCompany']);
        Route::post('/api/add/faculty', [AdminController::class, 'addFaculty']);

        Route::post('/api/update/student/{student_id}', [AdminController::class, 'updateStudent']);
        Route::post('/api/update/supervisor/{supervisor_id}', [AdminController::class, 'updateSupervisor']);
        Route::post('/api/update/company/{company_id}', [AdminController::class, 'updateCompany']);
        Route::post('/api/update/faculty/{faculty_id}', [AdminController::class, 'updateFaculty']);

        Route::delete('/api/delete/student/{student_id}', [AdminController::class, 'deleteStudent']);
        Route::delete('/api/delete/supervisor/{supervisor_id}', [AdminController::class, 'deleteSupervisor']);
        Route::delete('/api/delete/company/{company_id}', [AdminController::class, 'deleteCompany']);
        Route::delete('/api/delete/faculty/{faculty_id}', [AdminController::class, 'deleteFaculty']);

        Route::put('/api/enable/student/{student_id}', [AdminController::class, 'enableStudent']);
        Route::put('/api/enable/supervisor/{supervisor_id}', [AdminController::class, 'enableSupervisor']);
        Route::put('/api/enable/faculty/{faculty_id}', [AdminController::class, 'enableFaculty']);
        Route::put('/api/enable/company/{company_id}', [AdminController::class, 'enableCompany']);

        Route::put('/api/disable/student/{student_id}', [AdminController::class, 'disableStudent']);
        Route::put('/api/disable/supervisor/{supervisor_id}', [AdminController::class, 'disableSupervisor']);
        Route::put('/api/disable/faculty/{faculty_id}', [AdminController::class, 'disableFaculty']);
        Route::put('/api/disable/company/{company_id}', [AdminController::class, 'disableCompany']);
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
    });

    // View submitted file (Role checking is done in function)
    Route::get('/file/submission/{student_id}/{requirement_id}', [FileSubmissionContoller::class, 'showStudentDocument']);

    Route::post('/logout', [LoginController::class, 'logout']);
});
