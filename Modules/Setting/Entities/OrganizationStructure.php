<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'type',
        'head_name',
        'head_position',
        'level',
        'description',
        'tenant_id'
    ];

    public function parent()
    {
        return $this->belongsTo(OrganizationStructure::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(OrganizationStructure::class, 'parent_id');
    }

    public function getAllChildren()
    {
        return $this->children()->with('getAllChildren');
    }
}
