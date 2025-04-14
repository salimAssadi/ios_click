<?php

namespace Modules\Tenant\Models;

class DocumentArchive extends TenantModel
{
    protected $fillable = [
        'document_id',
        'archived_by',
        'reason',
        'archived_at'
    ];

    protected $casts = [
        'archived_at' => 'datetime'
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
