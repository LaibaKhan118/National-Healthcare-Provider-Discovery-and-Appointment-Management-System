<?php
// app/Http/Controllers/Doctor/DoctorProfileController.php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        abort_if(!$doctor, 404);

        $specializations = DB::table('specializations')->orderBy('specialization_name')->get();
        $hospitals = DB::table('hospitals')
            ->where('is_pending_verification', 0)
            ->orderBy('city')
            ->orderBy('hospital_name')
            ->get();

        $doctorSpecs = DB::table('doctor_specializations')
            ->where('doctor_id', $doctor->doctor_id)
            ->pluck('specialization_id')
            ->toArray();

        $doctorHospitals = DB::table('doctor_hospitals')
            ->where('doctor_id', $doctor->doctor_id)
            ->pluck('hospital_id')
            ->toArray();

        return view('doctor.profile.edit', compact('doctor', 'user', 'specializations', 'hospitals', 'doctorSpecs', 'doctorHospitals'));
    }

    public function update(Request $request)
{
    \Log::info('Profile update request', [
        'all_input' => $request->all(),
        'hospital_ids' => $request->input('hospital_ids'),
        'hospital_ids_filled' => $request->filled('hospital_ids'),
    ]);

    $user = auth()->user();
    $doctor = DB::table('doctors')->where('user_id', $user->user_id)->first();

    $request->validate([
        'license_number'    => 'nullable|string|max:50',
        'experience_years'  => 'nullable|integer|min:0',
        'consultation_fee'  => 'nullable|numeric|min:0',
        'city'              => 'nullable|string|max:100',
        'bio'               => 'nullable|string',
        'hospital_ids'      => 'nullable|array',
        'hospital_ids.*'    => 'integer|exists:hospitals,hospital_id',
        'specialization_ids'   => 'nullable|array',
        'specialization_ids.*' => 'integer|exists:specializations,specialization_id',
    ]);

    DB::beginTransaction();
    try {
        // Update doctor details
        DB::table('doctors')
            ->where('doctor_id', $doctor->doctor_id)
            ->update([
                'license_number'   => $request->input('license_number'),
                'experience_years' => $request->input('experience_years'),
                'consultation_fee' => $request->input('consultation_fee'),
                'city'             => $request->input('city'),
                'bio'              => $request->input('bio'),
            ]);

        // Sync specializations
        \Log::info('Starting specialization sync');
        DB::table('doctor_specializations')
            ->where('doctor_id', $doctor->doctor_id)
            ->delete();

        if ($request->filled('specialization_ids')) {
            $rows = [];
            foreach ($request->input('specialization_ids') as $specId) {
                $rows[] = [
                    'doctor_id'         => $doctor->doctor_id,
                    'specialization_id' => $specId,
                ];
            }
            DB::table('doctor_specializations')->insert($rows);
            \Log::info('Specializations synced successfully');
        }

        // Sync hospitals (multiple)
        \Log::info('About to start hospital sync section');
        DB::table('doctor_hospitals')
            ->where('doctor_id', $doctor->doctor_id)
            ->delete();
        \Log::info('Deleted existing doctor_hospitals records');

        \Log::info('Hospital sync check', [
            'filled' => $request->filled('hospital_ids'),
            'input' => $request->input('hospital_ids'),
            'hospital_ids_array' => (array) $request->input('hospital_ids', []),
        ]);

        if ($request->filled('hospital_ids')) {
            $rows = [];
            foreach ($request->input('hospital_ids') as $hospitalId) {
                $rows[] = [
                    'doctor_id'   => $doctor->doctor_id,
                    'hospital_id' => (int) $hospitalId,
                ];
            }
            \Log::info('Inserting hospitals', ['rows' => $rows, 'doctor_id' => $doctor->doctor_id]);
            DB::table('doctor_hospitals')->insert($rows);
            \Log::info('Hospitals inserted successfully');
        } else {
            \Log::info('No hospitals to insert - request.filled returned false');
        }

        DB::commit();
        return redirect()->route('doctor.profile.edit')
            ->with('success', 'Profile updated successfully!');

    } catch (\Exception $e) {
        \Log::error('Profile update failed with exception', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);
        DB::rollBack();
        return back()->withInput()
            ->with('error', 'Update failed: ' . $e->getMessage());
    }
}
}
