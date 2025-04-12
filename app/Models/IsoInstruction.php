<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoInstruction extends Model
{
    protected $connection = 'iso_dic';
    use HasFactory, Localizable;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'content',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    public function getNameAttribute()
    {
        return $this->getLocalizedAttribute('name');
    }

    public function getDescriptionAttribute()
    {
        return $this->getLocalizedAttribute('description');
    }

    public function procedures()
    {
        return $this->belongsToMany(Procedure::class, 'iso_instruction_procedures', 'instruction_id', 'procedure_id');
    }

    public function attachments()
    {
        return $this->hasMany(IsoInstructionAttachment::class, 'instruction_id');
    }
}
