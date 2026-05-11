<?php
// app/Http/Middleware/PatientMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || $user->role_id != 3) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Access denied. Patient only.');
        }

        if ($user->isSuspended()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been suspended.');
        }

        return $next($request);
    }
}
