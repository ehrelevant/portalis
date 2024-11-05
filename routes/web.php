<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('home/Index');
});

Route::get('/dashboard', function () {
    return Inertia::render('(authenticated)/dashboard/Index');
})->middleware('auth');

Route::get('/privacy', function () {
    return Inertia::render('privacy/Index');
});

Route::get('/account', function () {
    return Inertia::render('(authenticated)/account/Index');
})->middleware('auth');

Route::get('/login', function () {
    return Inertia::render('login/Index');
});

Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/login/send_pin', [LoginController::class, 'sendPin']);
Route::post('/logout', [LoginController::class, 'logout']);