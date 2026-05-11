<?php
// app/Models/Admin.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Admin extends Model
{
    use SoftDeletes;    
    protected $primaryKey = 'admin_id';
    protected $dates = ['deleted_at'];
    public $timestamps = false;
    protected $table = 'admins';

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
