<?php
// app/Models/Doctor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Doctor extends Model
{
    
    use SoftDeletes;
    protected $primaryKey = 'doctor_id';
    protected $dates = ['deleted_at'];
    public $timestamps = false;
    protected $table = 'doctors';

    protected $fillable = [
        'user_id',
        'license_number',
        'specialization',
        'experience_years',
        'consultation_fee',
        'bio',
        'city',
        'hospital_affiliation',
        'is_verified',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id', 'doctor_id');
    }

    public function availability()
    {
        return $this->hasMany(Availability::class, 'doctor_id', 'doctor_id');
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Appointment::class, 'doctor_id', 'appointment_id', 'doctor_id', 'appointment_id');
    }

    public function getAverageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewCount()
    {
        return $this->reviews()->count();
    }
}
