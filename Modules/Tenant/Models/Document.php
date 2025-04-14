<?php

namespace Modules\Tenant\Models;

class Document extends TenantModel
{
    protected $fillable = [
        'title',
        'description',
        'content',
        'status', // draft, pending_review, approved, archived
        'created_by',
        'updated_by',
        'google_doc_id'
    ];

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function approvals()
    {
        return $this->hasMany(DocumentApproval::class);
    }

    public function archive()
    {
        return $this->hasOne(DocumentArchive::class);
    }

    public function googleDocMapping()
    {
        return $this->hasOne(GoogleDocMapping::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
