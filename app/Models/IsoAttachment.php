<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoAttachment extends Model
{
    use HasFactory;
    protected $table = 'iso_attachments';

    protected $fillable = [
        'iso_system_id',
        'name_ar',
        'name_en',
        'type',
        'file_path',
        'is_published'
    ];

    public function isoSystem()
    {
        return $this->belongsTo(IsoSystem::class);
    }
}
