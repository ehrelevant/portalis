<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function show(): Response
    {
        $user = Auth::user();

        if ($user->role === 'student') {
            $wordpress_info = DB::table('students')
                ->where('students.id', $user->role_id)
                ->select('wordpress_name', 'wordpress_email')
                ->firstOrFail();

            return Inertia::render('account/Index', [
                "first_name" => ucwords($user->first_name),
                "middle_name" => ucwords($user->middle_name),
                "last_name" => ucwords($user->last_name),
                "role" => ucwords($user->role),
                "email" => $user->email,
                "wordpress_name" => $wordpress_info->wordpress_name,
                "wordpress_email" => $wordpress_info->wordpress_email,
            ]);
        } else {
            return Inertia::render('account/Index', [
                "first_name" => ucwords($user->first_name),
                "middle_name" => ucwords($user->middle_name),
                "last_name" => ucwords($user->last_name),
                "role" => ucwords($user->role),
                "email" => $user->email,
            ]);
        }
    }
}
