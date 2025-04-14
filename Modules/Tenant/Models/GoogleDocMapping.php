<?php

namespace Modules\Tenant\Models;

class GoogleDocMapping extends TenantModel
{
    protected $fillable = [
        'document_id',
        'google_doc_id',
        'google_doc_url',
        'last_synced_at'
    ];

    protected $casts = [
        'last_synced_at' => 'datetime'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
