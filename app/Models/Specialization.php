<?php
// app/Models/Specialization.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Specialization extends Model
{
    use SoftDeletes;
    
    protected $primaryKey = 'specialization_id';
    protected $dates = ['deleted_at'];
    public $timestamps = false;
    protected $table = 'specializations';

    protected $fillable = [
        'specialization_name',
    ];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_specializations', 'specialization_id', 'doctor_id');
    }
}
