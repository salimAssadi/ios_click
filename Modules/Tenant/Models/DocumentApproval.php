<?php

namespace Modules\Tenant\Models;

class DocumentApproval extends TenantModel
{
    protected $fillable = [
        'document_id',
        'reviewer_id',
        'status', // pending, approved, rejected
        'comments',
        'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
