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
        return view('auth.register');
    }

    public function register(Request $request)
{
    $request->validate([
        'first_name'       => 'required|string|max:100',
        'last_name'        => 'required|string|max:100',
        'email'            => 'required|email|unique:users',
        'password'         => 'required|min:8|confirmed',
        'role'             => 'required|in:patient,doctor',
        'hospital_id'      => 'nullable|exists:hospitals,hospital_id',
        'new_hospital_name'=> 'nullable|string|max:200',
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
            // 2. Create doctor record FIRST and capture the ID directly
            $doctorId = DB::table('doctors')->insertGetId([
                'user_id'    => $userId,
                'is_verified'=> 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Resolve hospital_id
            $hospitalId = null;

            // Priority 1: existing hospital selected from dropdown
            if ($request->filled('hospital_id')) {
                $hospitalId = (int) $request->hospital_id;
            }
            // Priority 2: new hospital name typed in
            elseif ($request->filled('new_hospital_name')) {
                $hospitalId = DB::table('hospitals')->insertGetId([
                    'hospital_name'            => trim($request->new_hospital_name),
                    'city'                     => null,
                    'address'                  => null,
                    'is_pending_verification'  => 1,
                    'created_at'               => now(),
                    'updated_at'               => now(),
                ]);
            }

            // 4. Link hospital to doctor
            if ($hospitalId) {
                DB::table('doctor_hospitals')->insert([
                    'doctor_id'   => $doctorId,  // Use captured ID directly
                    'hospital_id' => $hospitalId,
                ]);
            }

            DB::commit();

            Auth::loginUsingId($userId);
            return redirect()->route('doctor.profile.edit')
                ->with('message', 'Registration successful. Please complete your profile.');

        } else {
            // Patient registration
            DB::table('patients')->insert([
                'user_id'    => $userId,
                'created_at' => now(),
                'updated_at' => now(),
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
