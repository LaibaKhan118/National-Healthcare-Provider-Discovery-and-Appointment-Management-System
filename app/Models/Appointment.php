<?php
// app/Models/Appointment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;
    
    protected $primaryKey = 'appointment_id';
    protected $dates = ['deleted_at'];
    public $timestamps = false;
    protected $table = 'appointments';

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'appointment_date',
        'appointment_time',
        'appointment_status',
        'notes',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'appointment_id', 'appointment_id');
    }

    public function appointmentNote()
    {
        return $this->hasOne(AppointmentNote::class, 'appointment_id', 'appointment_id');
    }

    public function isPending()
    {
        return $this->appointment_status === 'pending';
    }

    public function isCompleted()
    {
        return $this->appointment_status === 'completed';
    }

    public function isCancelled()
    {
        return $this->appointment_status === 'cancelled';
    }
}
