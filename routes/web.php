<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\FileSubmissionContoller;
use App\Http\Controllers\InternEvaluationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\WeeklyReportController;
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
    Route::get('/dashboard', [DashboardController::class, 'redirectPhase']);
    Route::get('/dashboard/{phase}', [DashboardController::class, 'show'])->middleware(EnsureCorrectPhase::class);

    Route::get('/account', [AccountController::class, 'show']);

    Route::middleware([EnsureUserHasRole::class . ':student'])->group(function () {
        Route::get('/dashboard/pre/upload', function () {
            return Inertia::render('dashboard/pre/(student)/upload/Index');
        });
        Route::post('/dashboard/pre/submit', [DashboardController::class, 'submitStudentDocument']);
    });

    Route::middleware([EnsureUserHasRole::class . ':supervisor'])->group(function () {
        Route::get('/dashboard/during/report/{week}', [WeeklyReportController::class, 'show']);
        Route::post('/dashboard/during/report/submit', [WeeklyReportController::class, 'submit']);

        Route::get('/dashboard/during/final', [InternEvaluationController::class, 'show']);
        Route::post('/dashboard/during/final/submit', [InternEvaluationController::class, 'submit']);
    });

    Route::middleware([EnsureUserHasRole::class . ':faculty'])->group(function () {
        Route::get('/dashboard/pre/students', [FacultyController::class, 'showStudents']);
        Route::get('/dashboard/pre/students/{student_number}', [FacultyController::class, 'showStudent']);
        Route::post('/dashboard/pre/students/{student_number}/{requirement_id}/validate', [FacultyController::class, 'validateStudentSubmission']);
        Route::post('/dashboard/pre/students/{student_number}/{requirement_id}/invalidate', [FacultyController::class, 'invalidateStudentSubmission']);
        Route::post('/dashboard/pre/students/{student_number}/{requirement_id}/reject', [FacultyController::class, 'rejectStudentSubmission']);

        Route::get('/dashboard/pre/supervisors', [FacultyController::class, 'showSupervisors']);
        Route::get('/dashboard/pre/supervisors/{supervisor_id}', [FacultyController::class, 'showSupervisor']);
    });

    Route::get('/file/student/{student_number}/{requirement_id}', [FileSubmissionContoller::class, 'showStudentDocument']);

    Route::post('/logout', [LoginController::class, 'logout']);
});
