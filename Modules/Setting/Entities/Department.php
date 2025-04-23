<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \App\Models\BaseModel;
use Modules\Setting\Entities\Position;
use App\Traits\Localizable;

class Department extends BaseModel
{
    use HasFactory ,Localizable;

    protected $fillable = [
        'name_ar',
        'name_en',
        'parent_id',
        'description',
        'level'
    ];

    public function getNameAttribute()
    {
        return $this->getLocalizedAttribute('name');
    }
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function positions()
    {
        return $this->hasMany(Position::class, 'department_id');
    }

    public function getAllChildren()
    {
        return $this->children()->with('getAllChildren');
    }

    public function getAllPositions()
    {
        return $this->positions()->with('employees');
    }
}
