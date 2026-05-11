<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;
    
    protected $primaryKey = 'user_id';
    protected $dates = ['deleted_at'];
    public $timestamps = true;

    protected $fillable = [
        'email',
        'password_hash',
        'role_id',
        'account_status',
        'first_name',
        'last_name',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id', 'user_id');
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'user_id', 'user_id');
    }

    public function patient()
    {
        return $this->hasOne(Patient::class, 'user_id', 'user_id');
    }

    public function isSuspended()
    {
        return $this->account_status === 'suspended';
    }

    public function isActive()
    {
        return $this->account_status === 'active';
    }

    public function isAdmin()
    {
        return $this->role_id == 1;
    }

    public function isDoctor()
    {
        return $this->role_id == 2;
    }

    public function isPatient()
    {
        return $this->role_id == 3;
    }
}
