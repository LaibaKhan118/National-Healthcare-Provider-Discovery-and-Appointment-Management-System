<?php
// app/Http/Controllers/Admin/AdminDashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = DB::table('users')
            ->selectRaw("
                COUNT(*) as total_users,
                SUM(role_id = 1) as admin_count,
                SUM(role_id = 2) as doctor_count,
                SUM(role_id = 3) as patient_count,
                SUM(account_status = 'suspended') as suspended_count
            ")
            ->first();

        $pendingDoctors = DB::table('doctors')
            ->join('users', 'users.user_id', '=', 'doctors.user_id')
            ->where('doctors.is_verified', 0)
            ->select('doctors.doctor_id', 'users.first_name', 'users.last_name', 'users.email')
            ->orderByDesc('doctors.doctor_id')
            ->get();

        $topDoctors = DB::table('doctors')
            ->join('users', 'users.user_id', '=', 'doctors.user_id')
            ->where('doctors.is_verified', 1)
            ->leftJoin('reviews', 'reviews.doctor_id', '=', 'doctors.doctor_id')
            ->select('doctors.doctor_id', 'users.first_name', 'users.last_name', DB::raw('AVG(reviews.rating) as avg_rating'))
            ->groupBy('doctors.doctor_id', 'users.first_name', 'users.last_name')
            ->orderByDesc('avg_rating')
            ->limit(5)
            ->get();

        $recentAppointments = DB::table('appointments')
            ->join('doctors', 'doctors.doctor_id', '=', 'appointments.doctor_id')
            ->join('patients', 'patients.patient_id', '=', 'appointments.patient_id')
            ->join('users as du', 'du.user_id', '=', 'doctors.user_id')
            ->join('users as pu', 'pu.user_id', '=', 'patients.user_id')
            ->select(
                'appointments.appointment_id',
                'appointments.appointment_date',
                'appointments.appointment_time',
                'appointments.appointment_status',
                DB::raw("CONCAT(du.first_name, ' ', du.last_name) as doctor_name"),
                DB::raw("CONCAT(pu.first_name, ' ', pu.last_name) as patient_name")
            )
            ->orderByDesc('appointments.created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingDoctors', 'topDoctors', 'recentAppointments'));
    }
}
