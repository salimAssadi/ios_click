<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoSystem extends Model
{
    use HasFactory;

    protected $table = 'iso_systems'; 

    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'specification',
        'version',
        'image',
        'is_published'
    ];

    public function attachments()
    {
        return $this->hasMany(IsoAttachment::class);
    }

    public function specificationItems()
    {
        return $this->hasMany(IsoSpecificationItem::class);
    }
}
