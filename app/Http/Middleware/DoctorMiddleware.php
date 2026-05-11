<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || $user->role_id != 2) {
            return redirect('/login')->with('error', 'Unauthorized access');
        }

        if ($user->account_status == 'suspended') {
            return redirect('/login')->with('error', 'Your account has been suspended');
        }

        $doctor = DB::table('doctors')
            ->where('user_id', $user->user_id)
            ->first();

        if (!$doctor) {
            return redirect('/login')->with('error', 'Doctor profile not found');
        }

        // Allow access to profile edit page even if not verified
        if ($request->routeIs('doctor.profile.edit') || $request->routeIs('doctor.profile.update')) {
            return $next($request);
        }

        // For other routes, require verification
        if ($doctor->is_verified != 1) {
            return redirect('/doctor/profile/edit')->with('warning', 'Please complete your profile to activate your account');
        }

        return $next($request);
    }
}