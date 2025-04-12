<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentArchive extends Model
{
    protected $connection = 'iso_dic';
    protected $fillable = [
        'document_id',
        'archive_reason',
        'archived_by',
        'archived_at',
        'document_data'
    ];

    protected $casts = [
        'archived_at' => 'datetime',
        'document_data' => 'json'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
}
