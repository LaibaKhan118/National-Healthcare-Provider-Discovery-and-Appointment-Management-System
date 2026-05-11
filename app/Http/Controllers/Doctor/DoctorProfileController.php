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
        $hospitals = DB::table('hospitals')->orderBy('city')->orderBy('hospital_name')->get();

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
    $user = auth()->user();
    $doctor = DB::table('doctors')->where('user_id', $user->user_id)->first();

    $request->validate([
        'license_number'    => 'nullable|string|max:50',
        'experience_years'  => 'nullable|integer|min:0',
        'consultation_fee'  => 'nullable|numeric|min:0',
        'city'              => 'nullable|string|max:100',
        'bio'               => 'nullable|string',
        'hospital_id'       => 'nullable|exists:hospitals,hospital_id',
        'new_hospital_name' => 'nullable|string|max:200',
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
                'updated_at'       => now(),
            ]);

        // Sync specializations
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
        }
        // Sync hospital
        $hospitalId = null;

        if ($request->filled('hospital_id')) {
            // Existing hospital selected from dropdown
            $hospitalId = (int) $request->hospital_id;

        } elseif ($request->filled('new_hospital_name')) {
            // New hospital typed — create as pending
            $hospitalId = DB::table('hospitals')->insertGetId([
                'hospital_name'           => trim($request->new_hospital_name),
                'city'                    => null,
                'address'                 => null,
                'is_pending_verification' => 1,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);
        }
        
        dd([
            'hospitalId'   => $hospitalId,
            'doctor_id'    => $doctor->doctor_id,
            'doctor_obj'   => $doctor,
        ]);

        // Delete old hospital links and insert new one
        DB::table('doctor_hospitals')
            ->where('doctor_id', $doctor->doctor_id)
            ->delete();

        if ($hospitalId) {
            DB::table('doctor_hospitals')->insert([
                'doctor_id'   => $doctor->doctor_id,
                'hospital_id' => $hospitalId,
            ]);
        }

        DB::commit();
        return redirect()->route('doctor.profile.edit')
            ->with('success', 'Profile updated successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()
            ->with('error', 'Update failed: ' . $e->getMessage());
    }
}
}
