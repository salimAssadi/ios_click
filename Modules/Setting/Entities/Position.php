<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'title',
        'reports_to_id',
        'description'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function reportsTo()
    {
        return $this->belongsTo(Position::class, 'reports_to_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Position::class, 'reports_to_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function currentEmployee()
    {
        return $this->hasOne(Employee::class)->where('status', 'active');
    }
}
