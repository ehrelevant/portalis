<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginMail;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return back()->withErrors([
                'pin' => 'The provided pin has either expired or does not match the given email.',
            ]);
        }

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

            return redirect()->to('/dashboard');
        }

        return back()->withErrors([
            'pin' => 'The provided pin has either expired or does not match the given email.',
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

        $generated_pin = str_pad(random_int(0, 999999), 6, 0, STR_PAD_LEFT);

        try {
            $user = User::where('email', $email)->where('is_disabled', false)->firstOrFail();
            $user->password = Hash::make($generated_pin);
            $user->password_expiry = now()->addSeconds(intval(env('PIN_DURATION', 300)));
            $user->saveQuietly();
        } catch (ModelNotFoundException) {
            // On Failure, do NOT display error messages, but also do not send email.
            return back();
        }

        if (env('SEND_PIN_TO_EMAIL', false)) {
            Mail::to($email)->send(new LoginMail([
                'title' => 'CS 195 Portal - Login Code',
                'body' => 'Pin: ' . $generated_pin,
            ]));
        }

        // Only for testing purposes
        if (env('LOG_PIN_ON_CONSOLE', true)) {
            info('Pin: ' . $generated_pin);
        }

        // Only for testing purposes
        if (env('DISPLAY_PIN_ON_PAGE', false)) {
            return back()->with('info', $generated_pin);
        }

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

        return redirect('/login');
    }
}
