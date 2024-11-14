<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function show(): Response
    {
        $user = Auth::user();

        return Inertia::render('account/Index', [
            "first_name" => ucwords($user->first_name),
            "middle_name" => ucwords($user->middle_name),
            "last_name" => ucwords($user->last_name),
            "role" => ucwords($user->role),
            "email" => $user->email,
        ]);
    }
}
