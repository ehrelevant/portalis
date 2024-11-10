<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\FacultyMiddleware;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\SupervisorMiddleware;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Support\Facades\Auth;
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
    Route::get('/dashboard', [DashboardController::class, 'show']);

    Route::get('/account', function () {
        return Inertia::render('account/Index');
    });

    Route::post('/logout', [LoginController::class, 'logout']);
});
