<?php

namespace Modules\Tenant\Entities;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;
use Modules\Setting\Entities\Employee;


class User extends Authenticatable implements MustVerifyEmail
{   
    use HasRoles;
    use Notifiable;
    protected $guard_name = 'tenant';
    protected $table = 'users';

    protected $fillable = [
        'email',
        'password',
        'type',
        'phone_number',
        'profile',
        'lang',
        'subscription',
        'subscription_expire_date',
        'parent_id',
        'is_active',
        'session_id',
        'last_login_at',
        'last_login_ip',
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

   

    public function getNameAttribute()
    {
        return $this->employee->name;
    }



}
