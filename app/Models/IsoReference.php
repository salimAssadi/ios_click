<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoReference extends Model
{
    use HasFactory;

    protected $connection = 'iso_dic';

    protected $fillable = [
        'name_ar',
        'name_en',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    public function isoSystems()
    {
        return $this->belongsToMany(IsoSystem::class, 'iso_reference_systems', 'reference_id', 'iso_system_id');
    }

    public function attachments()
    {
        return $this->hasMany(IsoReferenceAttachment::class, 'reference_id');
    }
}
