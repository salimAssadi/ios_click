<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Models\TenantModel;

class DocumentApproval extends TenantModel
{
    protected $fillable = [
        'document_version_id',
        'approver_id',
        'status',
        'comments',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime'
    ];

    public function documentVersion()
    {
        return $this->belongsTo(DocumentVersion::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
