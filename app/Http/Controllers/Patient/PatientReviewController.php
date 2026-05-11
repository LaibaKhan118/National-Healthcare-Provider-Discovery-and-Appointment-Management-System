<?php
// app/Http/Controllers/Patient/PatientReviewController.php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientReviewController extends Controller
{
    public function create($appointmentId)
    {
        $user = Auth::user();
        $patient = $user->patient;

        $appointment = DB::table('appointments')
            ->where('appointment_id', $appointmentId)
            ->where('patient_id', $patient->patient_id)
            ->where('appointment_status', 'completed')
            ->first();

        abort_if(!$appointment, 404);

        $existingReview = DB::table('reviews')
            ->where('appointment_id', $appointmentId)
            ->first();

        abort_if($existingReview, 404);

        $doctor = DB::table('doctors')
            ->join('users', 'users.user_id', '=', 'doctors.user_id')
            ->where('doctors.doctor_id', $appointment->doctor_id)
            ->select('doctors.*', 'users.first_name', 'users.last_name')
            ->first();

        return view('patient.reviews.create', compact('appointment', 'doctor'));
    }

    public function store(Request $request, $appointmentId)
    {
        $user = Auth::user();
        $patient = $user->patient;

        $appointment = DB::table('appointments')
            ->where('appointment_id', $appointmentId)
            ->where('patient_id', $patient->patient_id)
            ->where('appointment_status', 'completed')
            ->first();

        abort_if(!$appointment, 404);

        $existingReview = DB::table('reviews')
            ->where('appointment_id', $appointmentId)
            ->first();

        abort_if($existingReview, 404);

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:500',
        ]);

        DB::table('reviews')->insert([
            'appointment_id' => $appointmentId,
            'patient_id' => $patient->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
            'created_at' => now(),
        ]);

        return redirect()->route('patient.dashboard')->with('success', 'Review submitted successfully!');
    }
}
