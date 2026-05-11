<?php
// app/Http/Controllers/Patient/PatientProfileController.php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $patient = $user->patient;

        abort_if(!$patient, 404);

        return view('patient.profile.edit', compact('user', 'patient'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $patient = $user->patient;

        abort_if(!$patient, 404);

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
        ]);

        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
            ]);

        DB::table('patients')
            ->where('patient_id', $patient->patient_id)
            ->update([
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'date_of_birth' => $request->input('date_of_birth'),
            ]);

        return redirect()->route('patient.dashboard')->with('success', 'Profile updated successfully.');
    }
    
    public function destroy(Request $request)
{
    $user = auth()->user();
    $patient = $user->patient;
    
    abort_if(!$patient, 404);

    $request->validate([
        'confirm_delete' => 'required|accepted',
    ]);

    try {
        $patient->delete();
        $user->delete();
        
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Account deleted successfully.');
        
    } catch (\Exception $e) {
        return back()->with('error', 'Error deleting account: ' . $e->getMessage());
    }
}
}
