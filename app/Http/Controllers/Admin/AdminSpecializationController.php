<?php
// app/Http/Controllers/Admin/AdminSpecializationController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSpecializationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->query('edit')) {
            $spec = DB::table('specializations')
                ->where('specialization_id', $request->query('edit'))
                ->first();
        } else {
            $spec = null;
        }

        $specializations = DB::table('specializations')
            ->select('specializations.*', DB::raw('COUNT(ds.doctor_id) as doctor_count'))
            ->leftJoin('doctor_specializations as ds', 'ds.specialization_id', '=', 'specializations.specialization_id')
            ->groupBy('specializations.specialization_id', 'specializations.specialization_name')
            ->orderBy('specializations.specialization_name')
            ->get();

        return view('admin.specializations.index', compact('specializations', 'spec'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'specialization_name' => 'required|string|unique:specializations,specialization_name',
        ]);

        DB::table('specializations')->insert([
            'specialization_name' => $request->input('specialization_name'),
        ]);

        return redirect()->route('admin.specializations.index')->with('success', 'Specialization added.');
    }

    public function update(Request $request, $specId)
    {
        $request->validate([
            'specialization_name' => 'required|string|unique:specializations,specialization_name,' . $specId . ',specialization_id',
        ]);

        DB::table('specializations')
            ->where('specialization_id', $specId)
            ->update(['specialization_name' => $request->input('specialization_name')]);

        return redirect()->route('admin.specializations.index')->with('success', 'Specialization updated.');
    }

    public function destroy($specId)
{
    try {
        $spec = Specialization::find($specId);
        abort_if(!$spec, 404);
        
        // Check if any doctors use this specialization
        $doctorCount = DB::table('doctor_specializations')
            ->where('specialization_id', $specId)
            ->count();
        
        if ($doctorCount > 0) {
            return back()->with('error', 'Cannot delete: ' . $doctorCount . ' doctor(s) use this specialization.');
        }
        
        $spec->delete();  // Soft delete
        
        return back()->with('success', 'Specialization deleted successfully.');
        
    } catch (\Exception $e) {
        return back()->with('error', 'Delete failed: ' . $e->getMessage());
    }
}
}
