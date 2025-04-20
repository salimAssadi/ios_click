<?php

namespace Modules\Document\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class DocumentVersion extends BaseModel
{
    use HasFactory;
    protected $table = 'document_versions';
    protected $fillable = [
        'document_id', 'version', 'issue_date', 'expiry_date', 'review_due_date',
        'status', 'approval_date', 'approved_by', 'storage_path', 'file_path', 'change_notes', 'is_active'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

   
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
    
    protected static function newFactory()
    {
        return \Modules\Document\Database\factories\DocumentVersionFactory::new();
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
