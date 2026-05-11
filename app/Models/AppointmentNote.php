<?php
// app/Models/AppointmentNote.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AppointmentNote extends Model
{
    use SoftDeletes;
    
    protected $primaryKey = 'note_id';
    protected $dates = ['deleted_at'];
    public $timestamps = false;
    protected $table = 'appointment_notes';

    protected $fillable = [
        'appointment_id',
        'note_content',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }
}
