<?php
// app/Http/Controllers/Admin/AdminHospitalController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminHospitalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->query('edit')) {
            $hospital = DB::table('hospitals')
                ->where('hospital_id', $request->query('edit'))
                ->first();
        } else {
            $hospital = null;
        }

        $hospitals = DB::table('hospitals')
            ->select('hospitals.*', DB::raw('COUNT(dh.doctor_id) as doctor_count'))
            ->leftJoin('doctor_hospitals as dh', 'dh.hospital_id', '=', 'hospitals.hospital_id')
            ->groupBy('hospitals.hospital_id', 'hospitals.hospital_name', 'hospitals.city', 'hospitals.address')
            ->orderBy('hospitals.city')
            ->orderBy('hospitals.hospital_name')
            ->get();

        return view('admin.hospitals.index', compact('hospitals', 'hospital'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hospital_name' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
        ]);

        DB::table('hospitals')->insert([
            'hospital_name' => $request->input('hospital_name'),
            'city' => $request->input('city'),
            'address' => $request->input('address'),
        ]);

        return redirect()->route('admin.hospitals.index')->with('success', 'Hospital added.');
    }

    public function update(Request $request, $hospitalId)
    {
        $request->validate([
            'hospital_name' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
        ]);

        DB::table('hospitals')
            ->where('hospital_id', $hospitalId)
            ->update([
                'hospital_name' => $request->input('hospital_name'),
                'city' => $request->input('city'),
                'address' => $request->input('address'),
            ]);

        return redirect()->route('admin.hospitals.index')->with('success', 'Hospital updated.');
    }

    public function destroy($hospitalId)
    {
        try {
            $hospital = Hospital::find($hospitalId);
            abort_if(!$hospital, 404);
            
            // Check if any doctors are affiliated
            $doctorCount = DB::table('doctor_hospitals')
                ->where('hospital_id', $hospitalId)
                ->count();

            if ($doctorCount > 0) {
                return back()->with('error', 'Cannot delete: ' . $doctorCount . ' doctor(s) affiliated with this hospital.');
            }

            $hospital->delete();  // Soft delete
            
            return redirect()->route('admin.hospitals.index')
                ->with('success', 'Hospital deleted successfully.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    public function verify($hospitalId)
    {
        DB::table('hospitals')
            ->where('hospital_id', $hospitalId)
            ->update(['is_pending_verification' => 0]);

        return back()->with('success', 'Hospital added to verified list');
    }
}
