<?php

namespace Modules\Tenant\Models;

class DocumentVersion extends TenantModel
{
    protected $fillable = [
        'document_id',
        'version_number',
        'content',
        'changes',
        'created_by'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
