<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \App\Models\BaseModel;
use App\Traits\Localizable;

class Position extends BaseModel
{
    use HasFactory;
    use Localizable;    

    protected $fillable = [
        'department_id',
        'title_ar',
        'title_en',
        'reports_to_id',
        'description'
    ];

    public function getNameAttribute()
    {
        return $this->getLocalizedAttribute('title');
    }
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
        return $this->hasMany(Employee::class)->where('status', 'active');
    }

    public function currentEmployee()
    {
        return $this->hasOne(Employee::class)->where('status', 'active');
    }
}
