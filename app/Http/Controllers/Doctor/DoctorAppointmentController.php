<?php
// app/Http/Controllers/Doctor/DoctorAppointmentController.php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        abort_if(!$doctor, 404);

        $status = $request->query('status', 'pending');

        $query = DB::table('appointments')
            ->join('patients', 'patients.patient_id', '=', 'appointments.patient_id')
            ->join('users', 'users.user_id', '=', 'patients.user_id')
            ->where('appointments.doctor_id', $doctor->doctor_id)
            ->select(
                'appointments.*',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as patient_name"),
                'users.email'
            );

        if ($status !== 'all') {
            $query->where('appointments.appointment_status', $status);
        }

        $appointments = $query->orderByDesc('appointments.appointment_date')
            ->orderByDesc('appointments.appointment_time')
            ->paginate(15);

        $statusCounts = DB::table('appointments')
            ->where('doctor_id', $doctor->doctor_id)
            ->selectRaw("
                SUM(appointment_status = 'pending') as pending_count,
                SUM(appointment_status = 'completed') as completed_count,
                SUM(appointment_status = 'cancelled') as cancelled_count,
                SUM(appointment_status = 'no_show') as no_show_count
            ")
            ->first();

        return view('doctor.appointments.index', compact('appointments', 'status', 'statusCounts'));
    }

    public function show($appointmentId)
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        $appointment = DB::table('appointments')
            ->join('patients', 'patients.patient_id', '=', 'appointments.patient_id')
            ->join('users', 'users.user_id', '=', 'patients.user_id')
            ->where('appointments.appointment_id', $appointmentId)
            ->where('appointments.doctor_id', $doctor->doctor_id)
            ->select(
                'appointments.*',
                'patients.patient_id',
                'users.first_name',
                'users.last_name',
                'users.email'
            )
            ->first();

        abort_if(!$appointment, 404);

        $note = DB::table('appointment_notes')
            ->where('appointment_id', $appointmentId)
            ->first();

        return view('doctor.appointments.show', compact('appointment', 'note'));
    }

    public function mark(Request $request, $appointmentId)
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        $appointment = DB::table('appointments')
            ->where('appointment_id', $appointmentId)
            ->where('doctor_id', $doctor->doctor_id)
            ->first();

        abort_if(!$appointment, 404);

        $request->validate([
            'status' => 'required|in:completed,cancelled,no_show',
        ]);

        DB::table('appointments')
            ->where('appointment_id', $appointmentId)
            ->update(['appointment_status' => $request->input('status')]);

        return redirect()->route('doctor.appointments.index')->with('success', 'Appointment status updated.');
    }

    public function addNote(Request $request, $appointmentId)
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        $appointment = DB::table('appointments')
            ->where('appointment_id', $appointmentId)
            ->where('doctor_id', $doctor->doctor_id)
            ->where('appointment_status', 'completed')
            ->first();

        abort_if(!$appointment, 404);

        $request->validate([
            'note_content' => 'required|string',
        ]);

        $existingNote = DB::table('appointment_notes')
            ->where('appointment_id', $appointmentId)
            ->first();

        if ($existingNote) {
            DB::table('appointment_notes')
                ->where('note_id', $existingNote->note_id)
                ->update([
                    'note_content' => $request->input('note_content'),
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('appointment_notes')->insert([
                'appointment_id' => $appointmentId,
                'note_content' => $request->input('note_content'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Note saved.');
    }
}
