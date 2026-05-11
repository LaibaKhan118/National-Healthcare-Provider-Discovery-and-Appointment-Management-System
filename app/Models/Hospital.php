<?php
// app/Models/Hospital.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Hospital extends Model
{
    use SoftDeletes;
    
    protected $primaryKey = 'hospital_id';
    protected $dates = ['deleted_at'];
    public $timestamps = false;
    protected $table = 'hospitals';

    protected $fillable = [
        'hospital_name',
        'city',
        'address',
    ];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_hospitals', 'hospital_id', 'doctor_id');
    }
}
