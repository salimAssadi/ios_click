<?php

namespace Modules\Document\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Modules\Document\Entities\Document;
use Modules\Document\Entities\RequestType;
use Modules\Document\Entities\Status;
use Modules\Document\Entities\DocumentVersion;
use App\Models\BaseModel;

class DocumentRequest extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'version_id',
        'request_type_id',
        'requested_by',
        'assigned_to',
        'notes',
        'status_id',
        'action_at',
        'action_by',
    ];
    const DEFAULT_REQUEST_STATUS = 'pending';

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function requestType()
    {
        return $this->belongsTo(RequestType::class);
    }

    public function Status()
    {
        return $this->belongsTo(Status::class,'status_id','id');
    }

    public function requestStatus()
    {
        return $this->belongsTo(Status::class,'status_id','id')->where('type','request');
    }

    public function approvalStatus()
    {
        return $this->belongsTo(Status::class,'status_id','id')->where('type','approval');
    }

    public function getRequestStatusBadgeAttribute()
    {
        if (!$this->requestStatus) {
            return null;
        }
        return '<span class="badge ' . $this->requestStatus->badge . '">' . __($this->requestStatus->name) . '</span>';
    }
    public function getApprovalStatusBadgeAttribute()
    {
        if (!$this->approvalStatus) {
            return null;
        }
        return '<span class="badge ' . $this->approvalStatus->badge . '">' . __($this->approvalStatus->name) . '</span>';
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    protected static function newFactory()
    {
        return \Modules\Document\Database\factories\DocumentRequestFactory::new();
    }
    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
    public function version()
    {
        return $this->belongsTo(DocumentVersion::class);
    }
  
}
