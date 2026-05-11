<?php
// app/Models/Patient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Patient extends Model
{
    use SoftDeletes;
    
    protected $primaryKey = 'patient_id';
    protected $dates = ['deleted_at'];
    public $timestamps = false;
    protected $table = 'patients';

    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'date_of_birth',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id', 'patient_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'patient_id', 'patient_id');
    }
}
