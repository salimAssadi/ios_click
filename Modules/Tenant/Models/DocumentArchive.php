<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Models\TenantModel;

class DocumentArchive extends TenantModel
{
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
