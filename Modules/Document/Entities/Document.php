<?php

namespace Modules\Document\Entities;

use App\Models\BaseModel;
use Modules\Iso\Entities\IsoSystem;
use Modules\Document\Entities\DocumentVersion;
use Modules\Tenant\Entities\User;
use Modules\Document\Entities\Category;
use Modules\Document\Entities\Status;
use Modules\Document\Entities\DocumentRequest;
use Modules\Setting\Entities\Employee;
use App\Traits\Localizable;

class Document extends BaseModel
{
    use Localizable;
    protected $fillable = [
        'title_ar', 'title_en', 'document_number', 'document_type', 'documentable_type', 'documentable_id', 'department',
        'description_ar', 'description_en', 'category_id', 'file_path', 'original_filename', 'mime_type', 
        'file_size', 'owner', 'created_by', 'creation_date',
        'status_id', 'obsoleted_date', 'notes','reviewer_ids','approver_id','preparer_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'reviewer_ids' => 'array',
        'approver_id' => 'integer',
        'preparer_id' => 'array',
    ];

    
    const DOCUMENT_TYPE_PROCEDURE = 'procedure';
    const DOCUMENT_TYPE_FORM = 'form';
    const DOCUMENT_TYPE_SAMPLE = 'sample';
    const DRAFT_DOCUMENT_STATUS_ID = 11;
    const NEW_VERSION_STATUS_ID = 17;
    
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
    
    public function getApproverAttribute()
    {
        return Employee::where('user_id', $this->approver_id)->with(['user','position','department'])->first();
    }
    
    public function getPreparerlistAttribute()
    {       
        $ids = json_decode($this->preparer_id ?? '[]', true);
        return Employee::whereIn('user_id', $ids)->with(['user','position','department'])->get();
    }
    
    public function getReviewerslistAttribute()
    {   
        $ids = json_decode($this->reviewer_ids ?? '[]', true);
        return Employee::whereIn('user_id', $ids)->with(['user','position','department'])->get();
    }
    
    /**
     * Get the parent documentable model (Procedure, Policy, etc).
     * This is a polymorphic relationship.
     */
    public function documentable()
    {
        return $this->morphTo();
    }
    
    /**
     * Get the related process class name and ID as a formatted string.
     * 
     * @return string|null
     */
    public function getRelatedProcessAttribute()
    {
        if (!$this->documentable_type || !$this->documentable_id) {
            return null;
        }
        
        return $this->documentable_type . ':' . $this->documentable_id;
    }
}
