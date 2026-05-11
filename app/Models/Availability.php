<?php
// app/Models/Availability.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Availability extends Model
{
    use SoftDeletes;
    
    protected $primaryKey = 'availability_id';
    protected $dates = ['deleted_at'];
    public $timestamps = false;
    protected $table = 'availability';

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_booked',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'doctor_id');
    }

    public static function getDayName($dayNumber)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        return $days[$dayNumber - 1] ?? 'Unknown';
    }
}
