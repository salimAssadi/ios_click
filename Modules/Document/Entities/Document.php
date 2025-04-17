<?php

namespace Modules\Document\Entities;

use App\Models\BaseModel;
use Modules\Iso\Entities\IsoSystem;
use Modules\Document\Entities\DocumentVersion;

class Document extends BaseModel
{
    protected $fillable = [
        'title',
        'document_number',
        'document_type',
        'department_id',
        'description',
        'file_path',
        'storage_path',
        'status',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'iso_system_id'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = [
        'status_badge',
        'download_url',
        'preview_url'
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

    public function isoSystem()
    {
        return $this->belongsTo(IsoSystem::class);
    }

    public function documentVersion()
    {
        return $this->hasOne(DocumentVersion::class)->latest();
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
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

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        if ($status === 'active') {
            return $query->approved();
        } elseif ($status === 'draft') {
            return $query->draft();
        } elseif ($status === 'archived') {
            return $query->archived();
        }
        return $query->where('status', $status);
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

        $displayStatus = $this->status === 'approved' ? 'active' : $this->status;

        return '<span class="' . ($badges[$this->status] ?? 'badge bg-secondary') . '">' . 
               __(ucfirst($displayStatus)) . '</span>';
    }

    public function getVersionBadgeAttribute()
    {
        return '<span class="badge bg-primary">v' . $this->version . '</span>';
    }

    public function getDownloadUrlAttribute()
    {
        return route('tenant.document.download', $this->id);
    }

    public function getPreviewUrlAttribute()
    {
        return route('tenant.document.preview', $this->id);
    }

    public function getStoragePathAttribute()
    {
        if (!$this->file_path) return null;
        
        return "tenants/{$this->created_by}/documents/{$this->document_type}/active/{$this->file_path}";
    }
}
