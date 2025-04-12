<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SampleAttachment extends Model
{
    use HasFactory;

    protected $connection = 'iso_dic';
    protected $table = 'sample_attachments';
    // protected $timestamps = false;
    protected $fillable = [
        'sample_id',
        'file_name',
        'original_name',
        'file_path',
        'mime_type',
        'file_size'
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

   
}
