<?php
// app/Http/Controllers/Auth/RegisterController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $hospitals = DB::table('hospitals')
            ->where('is_pending_verification', 0)
            ->orderBy('hospital_name')
            ->get();

        return view('auth.register', compact('hospitals'));
    }

    public function register(Request $request)
{
    $request->validate([
        'first_name'       => 'required|string|max:100',
        'last_name'        => 'required|string|max:100',
        'email'            => 'required|email|unique:users',
        'password'         => 'required|min:8|confirmed',
        'role'             => 'required|in:patient,doctor',
        'hospital_ids'     => 'nullable|array',
        'hospital_ids.*'   => 'integer|exists:hospitals,hospital_id',
    ]);

    $roleId = $request->role === 'doctor' ? 2 : 3;

    DB::beginTransaction();
    try {
        // 1. Create user
        $userId = DB::table('users')->insertGetId([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'password_hash' => password_hash($request->password, PASSWORD_BCRYPT),
            'role_id'       => $roleId,
            'account_status'=> 'active',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        if ($roleId == 2) {
            // 2. Create doctor record
            $doctorId = DB::table('doctors')->insertGetId([
                'user_id'    => $userId,
                'is_verified'=> 0,
            ]);

            // 3. Link hospitals to doctor
            if ($request->filled('hospital_ids')) {
                $rows = [];
                foreach ($request->input('hospital_ids') as $hospitalId) {
                    $rows[] = [
                        'doctor_id'   => $doctorId,
                        'hospital_id' => (int) $hospitalId,
                    ];
                }
                DB::table('doctor_hospitals')->insert($rows);
            }

            DB::commit();

            Auth::loginUsingId($userId);
            return redirect()->route('doctor.profile.edit')
                ->with('message', 'Registration successful. Please complete your profile.');

        } else {
            // Patient registration
            DB::table('patients')->insert([
                'user_id'    => $userId,
            ]);

            DB::commit();

            Auth::loginUsingId($userId);
            return redirect()->route('patient.dashboard');
        }

    } catch (\Exception $e) {
        DB::rollBack();
        return back()
            ->withInput()
            ->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
    }
}
}
