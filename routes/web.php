<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\EnsureCorrectPhase;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('home/Index');
})->name('home');

Route::get('/privacy', function () {
    return Inertia::render('privacy/Index');
});

Route::get('/login', function () {
    return Inertia::render('login/Index');
});

Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/login/send_pin', [LoginController::class, 'sendPin']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'redirectPhase']);
    Route::get('/dashboard/{phase}', [DashboardController::class, 'show'])->middleware(EnsureCorrectPhase::class);

    Route::get('/account', [AccountController::class, 'show']);

    Route::get('/dashboard/pre/upload', function () {
        return Inertia::render('dashboard/pre/(student)/upload/Index');
    })->middleware(EnsureUserHasRole::class . ':student');

    Route::post('/dashboard/pre/submit', [DashboardController::class, 'submitStudentDocument']);

    Route::get('/dashboard/pre/students/{student_number}', function (int $student_number) {
        // TODO: Move to a controller
        $student = DB::table('students')
            ->where('student_number', $student_number)
            ->join('users', 'students.student_number', '=', 'users.role_id')
            ->where('users.role', 'student')
            ->select(
                'students.student_number',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
            )
            ->firstOrFail();

        $submission_statuses = DB::table('submission_statuses')
            ->where('student_number', $student_number)
            ->join('requirements', 'submission_statuses.requirement_id', '=', 'requirements.id')
            ->select(
                'requirements.requirement_name',
                'submission_statuses.status'
            )
            ->get();

        return Inertia::render('dashboard/pre/(faculty)/students/Index', [
            'student' => $student,
            'submissions' => $submission_statuses,
        ]);
    })->middleware(EnsureUserHasRole::class . ':faculty');

    Route::post('/logout', [LoginController::class, 'logout']);
});
