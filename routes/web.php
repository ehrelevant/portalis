<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('home/Index');
});

Route::get('/dashboard', function () {
    return Inertia::render('(authenticated)/dashboard/Index');
});

Route::get('/privacy', function () {
    return Inertia::render('privacy/Index');
});

Route::get('/account', function () {
    return Inertia::render('(authenticated)/account/Index');
});

Route::get('/login', function () {
    return Inertia::render('login/Index');
});
