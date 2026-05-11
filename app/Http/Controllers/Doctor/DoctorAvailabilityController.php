<?php
// app/Http/Controllers/Doctor/DoctorAvailabilityController.php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorAvailabilityController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        abort_if(!$doctor, 404);

        $slots = DB::table('availability')
            ->where('doctor_id', $doctor->doctor_id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $daysArray = [];
        for ($day = 1; $day <= 7; $day++) {
            $daysArray[$day] = [
                'name' => $this->getDayName($day),
                'slots' => $slots->where('day_of_week', $day)->values(),
            ];
        }

        return view('doctor.availability.index', compact('doctor', 'daysArray'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        abort_if(!$doctor, 404);

        $request->validate([
            'day_of_week' => 'required|integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $existingSlot = DB::table('availability')
            ->where('doctor_id', $doctor->doctor_id)
            ->where('day_of_week', $request->input('day_of_week'))
            ->where('start_time', $request->input('start_time'))
            ->where('end_time', $request->input('end_time'))
            ->first();

        if ($existingSlot) {
            return redirect()->back()->with('error', 'This time slot already exists.');
        }

        DB::table('availability')->insert([
            'doctor_id' => $doctor->doctor_id,
            'day_of_week' => $request->input('day_of_week'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'is_booked' => 0,
        ]);

        return redirect()->route('doctor.availability.index')->with('success', 'Time slot added.');
    }

    public function destroy($slotId)
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        $slot = DB::table('availability')
            ->where('availability_id', $slotId)
            ->where('doctor_id', $doctor->doctor_id)
            ->first();

        abort_if(!$slot, 404);

        if ($slot->is_booked) {
            return redirect()->back()->with('error', 'Cannot delete a booked slot.');
        }

        DB::table('availability')->where('availability_id', $slotId)->delete();

        return redirect()->route('doctor.availability.index')->with('success', 'Time slot deleted.');
    }

    private function getDayName($dayNumber)
    {
        $days = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        return $days[$dayNumber] ?? 'Unknown';
    }
}
