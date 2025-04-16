<?php

namespace Modules\Document\Models;

use App\Models\BaseModel;

class Document extends BaseModel
{
    protected $fillable = [
        'title',
        'document_number',
        'category',
        'department',
        'description',
        'file_path',
        'version',
        'status',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime'
    ];

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'badge bg-warning',
            'pending_approval' => 'badge bg-info',
            'approved' => 'badge bg-success',
            'rejected' => 'badge bg-danger',
            'archived' => 'badge bg-secondary'
        ];

        return '<span class="' . ($badges[$this->status] ?? 'badge bg-secondary') . '">' . 
               __(ucfirst($this->status)) . '</span>';
    }

    public function getVersionBadgeAttribute()
    {
        return '<span class="badge bg-primary">v' . $this->version . '</span>';
    }
}
