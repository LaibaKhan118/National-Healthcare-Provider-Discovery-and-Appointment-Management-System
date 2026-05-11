<?php
// app/Http/Controllers/Admin/AdminDoctorController.php

namespace App\Http\Controllers\Admin;
use App\Models\Doctor;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDoctorController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        
        $query = DB::table('doctors')
            ->join('users', 'users.user_id', '=', 'doctors.user_id')
            ->leftJoin('doctor_specializations', 'doctor_specializations.doctor_id', '=', 'doctors.doctor_id')
            ->leftJoin('specializations', 'specializations.specialization_id', '=', 'doctor_specializations.specialization_id')
            ->select(
                'doctors.doctor_id',
                'users.user_id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.account_status',
                'doctors.is_verified',
                'doctors.license_number',
                'doctors.experience_years',
                'doctors.consultation_fee',
                DB::raw('GROUP_CONCAT(specializations.specialization_name SEPARATOR ", ") as specializations')
            );

        if ($status === 'pending') {
            $query->where('doctors.is_verified', 0);
        } elseif ($status === 'verified') {
            $query->where('doctors.is_verified', 1);
        } elseif ($status === 'suspended') {
            $query->where('users.account_status', 'suspended');
        }

        $doctors = $query
            ->groupBy('doctors.doctor_id')
            ->orderByDesc('doctors.doctor_id')
            ->paginate(15);

        return view('admin.doctors.index', compact('doctors', 'status'));
    }


    public function show($doctorId)
    {
        $doctor = DB::table('doctors')
        ->select(
            'doctors.doctor_id',
            'doctors.is_verified',
            'doctors.license_number',
            'doctors.specialization',
            'doctors.experience_years',
            'doctors.consultation_fee',
            'doctors.city',
            'doctors.hospital_affiliation',   // text field fallback
            'doctors.bio',
            'users.created_at',
            'users.user_id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.account_status'
        )
        ->join('users', 'users.user_id', '=', 'doctors.user_id')
        ->where('doctors.doctor_id', $doctorId)
        ->first();

        abort_if(!$doctor, 404);

        $specializations = DB::table('doctor_specializations')
        ->join('specializations', 'specializations.specialization_id', '=', 'doctor_specializations.specialization_id')
        ->where('doctor_id', $doctorId)
        ->pluck('specialization_name')
        ->toArray();

        $reviews = DB::table('reviews')
        ->where('doctor_id', $doctorId)
        ->get();

        // Get hospitals from junction table (approved + pending)
        $junctionHospitals = DB::table('doctor_hospitals')
            ->join('hospitals', 'hospitals.hospital_id', '=', 'doctor_hospitals.hospital_id')
            ->where('doctor_hospitals.doctor_id', $doctorId)
            ->select(
                'hospitals.hospital_id',
                'hospitals.hospital_name',
                'hospitals.city',
                'hospitals.address',
                'hospitals.is_pending_verification'
            )
            ->get();
        $approvedHospitals = $junctionHospitals->where('is_pending_verification', 0)->values();
        $pendingHospitals  = $junctionHospitals->where('is_pending_verification', 1)->values();

        // Fallback: if nothing in junction table, check the text field
        $hospitalAffiliationText = null;
        if ($junctionHospitals->isEmpty() && !empty($doctor->hospital_affiliation)) {
            $hospitalAffiliationText = $doctor->hospital_affiliation;
        }
        $appointments = DB::table('appointments')
            ->where('doctor_id', $doctorId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(appointment_status = 'pending') as pending_count,
                SUM(appointment_status = 'completed') as completed_count,
                SUM(appointment_status = 'cancelled') as cancelled_count
            ")
            ->first();

        $avgRating = $reviews->count() > 0 ? $reviews->avg('rating') : 0;
        $reviewCount = $reviews->count();

        return view('admin.doctors.show', compact('doctor', 'specializations','approvedHospitals', 'pendingHospitals', 'hospitalAffiliationText', 'appointments', 'avgRating', 'reviewCount'));
    }

    public function approve($doctorId)
    {
        DB::table('doctors')
            ->where('doctor_id', $doctorId)
            ->update(['is_verified' => 1]);

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor verified successfully.');
    }

    public function suspend(Request $request, $doctorId)
    {
        $doctor = DB::table('doctors')->where('doctor_id', $doctorId)->first();
        abort_if(!$doctor, 404);

        DB::table('users')
            ->where('user_id', $doctor->user_id)
            ->update(['account_status' => 'suspended']);

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor account suspended.');
    }

    public function reactivate($doctorId)
    {
        $doctor = DB::table('doctors')->where('doctor_id', $doctorId)->first();
        abort_if(!$doctor, 404);

        DB::table('users')
            ->where('user_id', $doctor->user_id)
            ->update(['account_status' => 'active']);

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor account reactivated.');
    }

    public function destroy($doctorId)
{
    $doctor = Doctor::find($doctorId);
    abort_if(!$doctor, 404);
    
    try {
        $doctor->delete();           // Soft delete doctor
        $doctor->user->delete();     // Soft delete user (cascade)
        
        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor deleted successfully.');
            
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error deleting doctor: ' . $e->getMessage());
    }
}
}
