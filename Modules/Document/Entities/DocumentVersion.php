<?php

namespace Modules\Document\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Modules\Document\Entities\Status;
use Modules\Reminder\Entities\Reminder;

class DocumentVersion extends BaseModel
{
    use HasFactory;
    
    protected $table = 'document_versions';
    
    protected $fillable = [
        'document_id', 'version', 'issue_date', 'expiry_date','reminder_days', 'review_due_date',
        'status_id', 'approval_date', 'approved_by', 'storage_path', 'file_path', 
        'change_notes', 'is_active', 'created_by', 'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'issue_date' => 'datetime',
        'expiry_date' => 'datetime',
        'reminder_days' => 'integer',
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
       return $this->belongsTo(Status::class,'status_id','id')->where('type','revision');
   }

    /**
     * Get the document that owns the version.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the user who approved the document.
     */
    public function approver()
    {
        return $this->belongsTo(\Modules\Tenant\Entities\User::class, 'approved_by');
    }

    /**
     * Get the user who created the document.
     */
    public function creator()
    {
        return $this->belongsTo(\Modules\Tenant\Entities\User::class, 'created_by');
    }

    public function getStatusBadgeAttribute()
    {
        if (!$this->status) {
            return null;
        }
        return '<span class="badge ' . $this->status->badge . '">' . __($this->status->name) . '</span>';
    }
    /**
     * Get all reminders associated with this document version through the remindable relationship
     */
    public function reminders()
    {
        return $this->morphMany(Reminder::class, 'remindable');
    }
    
    /**
     * Get days remaining until expiry
     *
     * @return int|null
     */
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }
        
        $now = now();
        if ($now->greaterThan($this->expiry_date)) {
            return 0;
        }
        
        return $now->diffInDays($this->expiry_date);
    }
    
    public function revisions()
    {
        return $this->hasMany(DocumentRevision::class , 'version_id');
    }

    public function getLatestRevisionAttribute()
    {
        return $this->revisions()->latest()->first();
    }
}
