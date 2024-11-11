<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WebsiteState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function redirectPhase(): RedirectResponse
    {
        $phase = WebsiteState::findOrFail(1)->phase;
        return redirect('/dashboard/pre');
    }

    public function show(string $phase): Response
    {
        switch (Auth::user()->role) {
            case User::ROLE_STUDENT:
                return Inertia::render('dashboard/' . $phase . '/(student)/Index');
            case User::ROLE_SUPERVISOR:
                return Inertia::render('dashboard/' . $phase . '/(supervisor)/Index');
            case User::ROLE_FACULTY:
                return Inertia::render('dashboard/' . $phase . '/(faculty)/Index');
            case User::ROLE_ADMIN:
                return Inertia::render('dashboard/' . $phase . '/(admin)/Index');
        }

        abort(404);
    }
}
