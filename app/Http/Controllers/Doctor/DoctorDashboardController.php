<?php
// app/Http/Controllers/Doctor/DoctorDashboardController.php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        abort_if(!$doctor, 404);

        $doctorId = $doctor->doctor_id;

        $todayAppointments = DB::table('appointments')
            ->join('patients', 'patients.patient_id', '=', 'appointments.patient_id')
            ->join('users', 'users.user_id', '=', 'patients.user_id')
            ->where('appointments.doctor_id', $doctorId)
            ->where('appointments.appointment_date', now()->toDateString())
            ->where('appointments.appointment_status', 'pending')
            ->select('appointments.*', DB::raw("CONCAT(users.first_name, ' ', users.last_name) as patient_name"), 'users.email')
            ->orderBy('appointments.appointment_time')
            ->get();

        $upcomingAppointments = DB::table('appointments')
            ->join('patients', 'patients.patient_id', '=', 'appointments.patient_id')
            ->join('users', 'users.user_id', '=', 'patients.user_id')
            ->where('appointments.doctor_id', $doctorId)
            ->where('appointments.appointment_status', 'pending')
            ->where('appointments.appointment_date', '>', now()->toDateString())
            ->select('appointments.*', DB::raw("CONCAT(users.first_name, ' ', users.last_name) as patient_name"))
            ->orderBy('appointments.appointment_date')
            ->orderBy('appointments.appointment_time')
            ->limit(10)
            ->get();

        $stats = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(appointment_status = 'pending') as pending_count,
                SUM(appointment_status = 'completed') as completed_count,
                SUM(appointment_status = 'cancelled') as cancelled_count
            ")
            ->first();

        $averageRating = DB::table('reviews')
            ->where('doctor_id', $doctorId)
            ->avg('rating') ?? 0;

        $reviewCount = DB::table('reviews')
            ->where('doctor_id', $doctorId)
            ->count();

        $openSlots = DB::table('availability')
            ->where('doctor_id', $doctorId)
            ->where('is_booked', 0)
            ->count();

        return view('doctor.dashboard', compact('doctor', 'todayAppointments', 'upcomingAppointments', 'stats', 'averageRating', 'reviewCount', 'openSlots'));
    }
}
