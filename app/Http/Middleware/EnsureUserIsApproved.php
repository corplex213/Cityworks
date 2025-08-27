<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsApproved
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->is_approved) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account is pending approval by an administrator.']);
        }
        return $next($request);
    }
}