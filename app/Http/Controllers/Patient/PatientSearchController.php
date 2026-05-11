<?php
// app/Http/Controllers/Patient/PatientSearchController.php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientSearchController extends Controller
{
    public function welcome()
    {
        $topDoctors = DB::table('doctors')
            ->where('is_verified', 1)
            ->join('users', 'users.user_id', '=', 'doctors.user_id')
            ->leftJoin('reviews', 'reviews.doctor_id', '=', 'doctors.doctor_id')
            ->select(
                'doctors.doctor_id',
                'users.first_name',
                'users.last_name',
                'doctors.specialization',
                'doctors.consultation_fee',
                'doctors.city',
                DB::raw('COUNT(reviews.review_id) as review_count'),
                DB::raw('COALESCE(AVG(reviews.rating), 0) as avg_rating')
            )
            ->groupBy('doctors.doctor_id', 'users.first_name', 'users.last_name', 'doctors.specialization', 'doctors.consultation_fee', 'doctors.city')
            ->orderByDesc('avg_rating')
            ->limit(6)
            ->get();

        return view('welcome', compact('topDoctors'));
    }

    public function search(Request $request)
    {
        $specialization = $request->query('specialization', '');
        $city = $request->query('city', '');
        $minFee = $request->query('min_fee', 0);
        $maxFee = $request->query('max_fee', 100000);
        $minRating = $request->query('min_rating', 0);

        $query = DB::table('doctors')
            ->where('is_verified', 1)
            ->join('users', 'users.user_id', '=', 'doctors.user_id')
            ->leftJoin('reviews', 'reviews.doctor_id', '=', 'doctors.doctor_id')
            ->select(
                'doctors.doctor_id',
                'users.first_name',
                'users.last_name',
                'doctors.specialization',
                'doctors.experience_years',
                'doctors.consultation_fee',
                'doctors.city',
                'doctors.bio',
                DB::raw('COUNT(reviews.review_id) as review_count'),
                DB::raw('COALESCE(AVG(reviews.rating), 0) as avg_rating')
            )
            ->groupBy('doctors.doctor_id', 'users.first_name', 'users.last_name', 'doctors.specialization', 'doctors.experience_years', 'doctors.consultation_fee', 'doctors.city', 'doctors.bio');

        if ($specialization) {
            $query->where('doctors.specialization', $specialization);
        }

        if ($city) {
            $query->where('doctors.city', $city);
        }

        if ($minFee) {
            $query->where('doctors.consultation_fee', '>=', $minFee);
        }

        if ($maxFee && $maxFee < 100000) {
            $query->where('doctors.consultation_fee', '<=', $maxFee);
        }

        if ($minRating) {
            $query->havingRaw('AVG(reviews.rating) >= ?', [$minRating]);
        }

        $doctors = $query->orderByDesc('avg_rating')->paginate(15);

        $specializations = DB::table('doctors')
            ->where('is_verified', 1)
            ->distinct('specialization')
            ->pluck('specialization');

        $cities = DB::table('doctors')
            ->where('is_verified', 1)
            ->distinct('city')
            ->pluck('city');

        return view('patient.search', compact('doctors', 'specializations', 'cities', 'specialization', 'city', 'minFee', 'maxFee', 'minRating'));
    }

    public function showDoctor($doctorId)
    {
        $doctor = DB::table('doctors')
            ->where('doctor_id', $doctorId)
            ->where('is_verified', 1)
            ->join('users', 'users.user_id', '=', 'doctors.user_id')
            ->select('doctors.*', 'users.first_name', 'users.last_name', 'users.email')
            ->first();

        abort_if(!$doctor, 404);

        $averageRating = DB::table('reviews')
            ->where('doctor_id', $doctorId)
            ->avg('rating') ?? 0;

        $reviewCount = DB::table('reviews')
            ->where('doctor_id', $doctorId)
            ->count();

        $reviews = DB::table('reviews')
            ->where('doctor_id', $doctorId)
            ->join('patients', 'patients.patient_id', '=', 'reviews.patient_id')
            ->join('users', 'users.user_id', '=', 'patients.user_id')
            ->select('reviews.*', DB::raw("CONCAT(users.first_name, ' ', users.last_name) as patient_name"))
            ->orderByDesc('reviews.created_at')
            ->limit(5)
            ->get();

        $availableSlots = DB::table('availability')
            ->where('doctor_id', $doctorId)
            ->where('is_booked', 0)
            ->select('availability_id', 'day_of_week', 'start_time', 'end_time')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
        
        $dayNames = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];

        return view('patient.doctors.show', compact('doctor', 'averageRating', 'reviewCount', 'reviews', 'availableSlots', 'dayNames'));
    }
}
