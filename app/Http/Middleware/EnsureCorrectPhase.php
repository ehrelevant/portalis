<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\WebsiteState;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCorrectPhase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestedPhase = $request->route()->parameter('phase');
        $currentPhase = WebsiteState::findOrFail(1)->phase;

        // Limits users from entering pages not for the current phase
        if ($requestedPhase === $currentPhase || in_array(Auth::user()->role, [User::ROLE_ADMIN])) {
            return $next($request);
        }

        // Redirect user to page for current phase otherwise
        return redirect('/dashboard/' . $currentPhase);
    }
}
