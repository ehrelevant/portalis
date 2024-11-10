<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function show(): Response
    {
        switch (Auth::user()->role) {
            case User::ROLE_STUDENT:
                return Inertia::render('dashboard/(student)/Index');
            case User::ROLE_SUPERVISOR:
                return Inertia::render('dashboard/(supervisor)/Index');
            case User::ROLE_FACULTY:
                return Inertia::render('dashboard/(faculty)/Index');
            case User::ROLE_ADMIN:
                return Inertia::render('dashboard/(admin)/Index');
        }
    }
}
