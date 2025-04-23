<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position_id',
        'name',
        'email',
        'phone',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function department()
    {
        return $this->hasOneThrough(
            Department::class,
            Position::class,
            'id', // Position key
            'id', // Department key
            'position_id', // Employee key
            'department_id' // Position key
        );
    }

    public function reportsTo()
    {
        return $this->hasOneThrough(
            Employee::class,
            Position::class,
            'id', // Position key
            'position_id', // Employee key
            'position_id', // Employee key
            'reports_to_id' // Position key
        )->where('employees.status', 'active');
    }
}
