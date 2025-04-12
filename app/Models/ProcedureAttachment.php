<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProcedureAttachment extends Model
{
    use HasFactory;

    protected $connection = 'iso_dic';
    protected $table = 'procedure_attachments';

    protected $fillable = [
        'procedure_id',
        'file_name',
        'original_name',
        'file_path',
        'mime_type',
        'file_size'
    ];

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

   
}
