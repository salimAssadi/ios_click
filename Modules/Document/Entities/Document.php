<?php

namespace Modules\Document\Entities;

use App\Models\BaseModel;
use Modules\Iso\Entities\IsoSystem;
use Modules\Document\Entities\DocumentVersion;
use Modules\Tenant\Entities\User;
use Modules\Document\Entities\Category;
use Modules\Document\Entities\Status;
use Modules\Document\Entities\DocumentRequest;
use App\Traits\Localizable;

class Document extends BaseModel
{
    use Localizable;
    protected $fillable = [
        'title_ar', 'title_en', 'document_number', 'document_type', 'related_process', 'department',
        'description_ar', 'description_en', 'category_id', 'file_path', 'original_filename', 'mime_type', 
        'file_size', 'owner', 'created_by', 'creation_date', 'issue_date', 'expiry_date', 
        'status_id', 'obsoleted_date', 'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function getTitleAttribute(){
        return $this->getLocalizedAttribute('title');
    }
    
    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->where('type', 'document');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lastVersion()
    {
        return $this->hasOne(DocumentVersion::class)->where('is_active', true)->latest();
    }

    public function scopeByStatus($query, $status)
    {
        return $this->status()->where('id', $status);
    }

    public function scopeByType($query, $documentType)
    {
        return $query->where('document_type', $documentType);
    }

    public function getStatusBadgeAttribute()
    {
        if (!$this->status) {
            return null;
        }
        return '<span class="badge ' . $this->status->badge . '">' . __($this->status->name) . '</span>';
    }

    public function reviewRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
