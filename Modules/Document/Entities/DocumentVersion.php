<?php

namespace Modules\Document\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Modules\Document\Entities\Status;

class DocumentVersion extends BaseModel
{
    use HasFactory;
    
    protected $table = 'document_versions';
    
    protected $fillable = [
        'document_id', 'version', 'issue_date', 'expiry_date', 'review_due_date',
        'status_id', 'approval_date', 'approved_by', 'storage_path', 'file_path', 
        'change_notes', 'is_active', 'created_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'issue_date' => 'datetime',
        'expiry_date' => 'datetime',
        'review_due_date' => 'datetime',
        'approval_date' => 'datetime',
        'is_active' => 'boolean',
        'version' => 'float',
        'status_id' => 'integer'
    ];

    /**
     * Valid status values
     */
   public function status()
   {
       return $this->belongsTo(Status::class,'status_id','id')->where('type','document');
   }

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
        if (!$this->status) {
            return null;
        }
        return '<span class="badge ' . $this->status->badge . '">' . __($this->status->name) . '</span>';
    }

    public function getVersionBadgeAttribute()
    {
        return '<span class="badge bg-primary">v' . number_format($this->version, 1) . '</span>';
    }

    /**
     * Set the status attribute with validation
     */
  

    /**
     * Get the status attribute with default
     */
  
}
