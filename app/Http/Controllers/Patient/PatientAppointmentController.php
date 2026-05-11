<?php
// app/Http/Controllers/Patient/PatientAppointmentController.php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientAppointmentController extends Controller
{
    public function show($appointmentId)
{
    $patient = auth()->user()->patient;

    $appointment = DB::table('appointments')
        ->select(
            'appointments.*',
            'doctors.specialization',
            'doctors.consultation_fee',
            'doctors.city',
            'users.first_name',
            'users.last_name',
            'users.email'
        )
        ->join('doctors', 'doctors.doctor_id', '=', 'appointments.doctor_id')
        ->join('users', 'users.user_id', '=', 'doctors.user_id')
        ->where('appointments.appointment_id', $appointmentId)
        ->where('appointments.patient_id', $patient->patient_id)
        ->first();

    if (!$appointment) {
        abort(404, 'Appointment not found');
    }

    $note = DB::table('appointment_notes')
        ->where('appointment_id', $appointmentId)
        ->first();

    $review = DB::table('reviews')
        ->where('appointment_id', $appointmentId)
        ->first();

    return view('patient.appointments.show', compact('appointment', 'note', 'review'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $patient = $user->patient;

        $request->validate([
            'doctor_id' => 'required|integer|exists:doctors,doctor_id',
            'availability_id' => 'required|integer|exists:availability,availability_id',
        ]);

        $doctorId = $request->input('doctor_id');
        $availabilityId = $request->input('availability_id');

        $doctor = DB::table('doctors')->where('doctor_id', $doctorId)->where('is_verified', 1)->first();
        abort_if(!$doctor, 404);

        $slot = DB::table('availability')
            ->where('availability_id', $availabilityId)
            ->where('doctor_id', $doctorId)
            ->where('is_booked', 0)
            ->first();

        abort_if(!$slot, 404);

        DB::beginTransaction();
        try {
            $appointmentId = DB::table('appointments')->insertGetId([
                'doctor_id' => $doctorId,
                'patient_id' => $patient->patient_id,
                'appointment_date' => now()->toDateString(),
                'appointment_time' => $slot->start_time,
                'appointment_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('availability')
                ->where('availability_id', $availabilityId)
                ->update(['is_booked' => 1]);

            DB::commit();

            return redirect()->route('patient.appointments.show', $appointmentId)->with('success', 'Appointment booked successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error booking appointment.');
        }
    }

    public function cancel($appointmentId)
{
    $user = Auth::user();
    $patient = $user->patient;

    $appointment = Appointment::where('appointment_id', $appointmentId)
        ->where('patient_id', $patient->patient_id)
        ->where('appointment_status', 'pending')
        ->first();

    abort_if(!$appointment, 404);

    try {
        // Update status to cancelled (NOT soft delete - this is different)
        $appointment->update(['appointment_status' => 'cancelled']);
        
        // Release the booked slot
        DB::table('availability')
            ->where('doctor_id', $appointment->doctor_id)
            ->where('is_booked', 1)
            ->limit(1)
            ->update(['is_booked' => 0]);

        return redirect()->route('patient.dashboard')
            ->with('success', 'Appointment cancelled successfully.');
            
    } catch (\Exception $e) {
        return back()->with('error', 'Cancel failed: ' . $e->getMessage());
    }
}
}
