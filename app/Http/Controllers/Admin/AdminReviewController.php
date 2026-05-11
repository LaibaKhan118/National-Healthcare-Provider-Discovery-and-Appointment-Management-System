<?php
// app/Http/Controllers/Admin/AdminReviewController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $filterRating = $request->query('rating', 'all');

        $query = DB::table('reviews')
            ->join('doctors', 'doctors.doctor_id', '=', 'reviews.doctor_id')
            ->join('patients', 'patients.patient_id', '=', 'reviews.patient_id')
            ->join('users as du', 'du.user_id', '=', 'doctors.user_id')
            ->join('users as pu', 'pu.user_id', '=', 'patients.user_id')
            ->select(
                'reviews.review_id',
                'reviews.rating',
                'reviews.comment',
                'reviews.created_at',
                DB::raw("CONCAT(du.first_name, ' ', du.last_name) as doctor_name"),
                DB::raw("CONCAT(pu.first_name, ' ', pu.last_name) as patient_name")
            );

        if ($filterRating !== 'all') {
            $query->where('reviews.rating', $filterRating);
        }

        $reviews = $query->orderByDesc('reviews.created_at')->paginate(20);

        return view('admin.reviews.index', compact('reviews', 'filterRating'));
    }

    public function destroy($reviewId)
{
    try {
        $review = Review::find($reviewId);
        abort_if(!$review, 404);
        
        $review->delete();  // Soft delete
        
        return back()->with('success', 'Review deleted successfully.');
        
    } catch (\Exception $e) {
        return back()->with('error', 'Delete failed: ' . $e->getMessage());
    }
}
}
