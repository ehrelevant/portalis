<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

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

        $user = User::where('email', $credentials['email'])->firstOrFail();
        if ($user->password_expiry < now()) {
            $user->password = null;
            $user->password_expiry = null;
            $user->saveQuietly();

            return back()->withErrors([
                'pin' => 'The provided pin has either expired or does not match the given email.',
            ]);
        }

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['pin']])) {
            $request->session()->regenerate();

            // Deletes PIN
            $user->password = null;
            $user->password_expiry = null;
            $user->saveQuietly();

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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
        ]);
        $email = $credentials['email'];

        $generated_pin = Str::random(6);

        $user = User::where('email', $email)->firstOrFail();
        $user->password = Hash::make($generated_pin);
        $user->password_expiry = now()->addMinutes(5);
        $user->saveQuietly();

        info('Pin: ' . $generated_pin);

        return back();
    }

    /**
     * Handles manual logouts
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
