<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoogleDocMapping extends Model
{
    use SoftDeletes;

    protected $connection = 'iso_dic';
    
    protected $fillable = [
        'document_id',
        'document_type',
        'google_doc_id',
        'google_doc_url',
        'metadata',
        'last_synced_at',
        'sync_status',
        'error_message'
    ];

    protected $casts = [
        'metadata' => 'json',
        'last_synced_at' => 'datetime'
    ];

    public function document()
    {
        return $this->morphTo();
    }

    public function policy()
    {
        return $this->belongsTo(IsoPolicy::class, 'document_id')
            ->where('document_type', 'policy');
    }

    public function instruction()
    {
        return $this->belongsTo(IsoInstruction::class, 'document_id')
            ->where('document_type', 'instruction');
    }

    public function reference()
    {
        return $this->belongsTo(IsoReference::class, 'document_id')
            ->where('document_type', 'reference');
    }
}
