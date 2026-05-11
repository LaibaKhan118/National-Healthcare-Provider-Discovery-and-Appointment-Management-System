<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = DB::table('users')->where('email', $request->email)->first();

    if (!$user) {
        return back()->withErrors(['email' => 'Account not found. Please register first.'])->withInput();
    }

    if ($user->account_status === 'suspended') {
        return back()->withErrors(['email' => 'Your account has been suspended. Please contact admin.'])->withInput();
    }

    if (!password_verify($request->password, $user->password_hash)) {
        return back()->withErrors(['password' => 'Invalid password. Please try again.'])->withInput();
    }

    Auth::loginUsingId($user->user_id);
    $request->session()->regenerate();

    if ($user->role_id == 1) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role_id == 2) {
        $doctor = DB::table('doctors')->where('user_id', $user->user_id)->first();
        if ($doctor->is_verified == 0) {
            return redirect()->route('doctor.profile.edit')->with('warning', 'Please complete your profile. Your account is pending approval.');
        }
        return redirect()->route('doctor.dashboard');
    } else {
        return redirect()->route('patient.dashboard');
    }
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')->with('success', 'Logged out successfully.');
    }
}
