<?php
// app/Http/Controllers/Patient/PatientDashboardController.php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $patient = $user->patient;

        abort_if(!$patient, 404);

        $patientId = $patient->patient_id;

        $appointments = DB::table('appointments')
            ->join('doctors', 'doctors.doctor_id', '=', 'appointments.doctor_id')
            ->join('users', 'users.user_id', '=', 'doctors.user_id')
            ->where('appointments.patient_id', $patientId)
            ->select(
                'appointments.*',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as doctor_name"),
                'doctors.specialization',
                'doctors.consultation_fee'
            )
            ->orderByDesc('appointments.appointment_date')
            ->orderByDesc('appointments.appointment_time')
            ->limit(10)
            ->get();

        $nextAppointment = DB::table('appointments')
            ->join('doctors', 'doctors.doctor_id', '=', 'appointments.doctor_id')
            ->join('users', 'users.user_id', '=', 'doctors.user_id')
            ->where('appointments.patient_id', $patientId)
            ->where('appointments.appointment_status', 'pending')
            ->where('appointments.appointment_date', '>=', now()->toDateString())
            ->select(
                'appointments.*',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as doctor_name"),
                'doctors.specialization'
            )
            ->orderBy('appointments.appointment_date')
            ->orderBy('appointments.appointment_time')
            ->first();

        $stats = DB::table('appointments')
            ->where('patient_id', $patientId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(appointment_status = 'pending') as pending_count,
                SUM(appointment_status = 'completed') as completed_count,
                SUM(appointment_status = 'cancelled') as cancelled_count
            ")
            ->first();

        $pendingReviews = DB::table('appointments')
            ->where('appointments.patient_id', $patientId)
            ->where('appointments.appointment_status', 'completed')
            ->leftJoin('reviews', 'reviews.appointment_id', '=', 'appointments.appointment_id')
            ->whereNull('reviews.review_id')
            ->join('doctors', 'doctors.doctor_id', '=', 'appointments.doctor_id')
            ->join('users', 'users.user_id', '=', 'doctors.user_id')
            ->select('appointments.appointment_id', DB::raw("CONCAT(users.first_name, ' ', users.last_name) as doctor_name"), 'appointments.appointment_date')
            ->orderByDesc('appointments.appointment_date')
            ->limit(3)
            ->get();

        return view('patient.dashboard', compact('patient', 'appointments', 'nextAppointment', 'stats', 'pendingReviews'));
    }
}
