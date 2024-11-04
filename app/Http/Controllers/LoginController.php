<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'pin' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'pin' => 'The provided pin does not match the given email.',
        ]);
    }

    /**
     * Handle sending of PINs
     */
    public function sendPin(Request $request): RedirectResponse
    {
        $email = $request->validate([
            'email' => ['required', 'email']
        ])['email'];

        return back();
    }
}
