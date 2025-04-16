<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Tenant\Models\TenantModel;

class Request extends TenantModel
{
    use HasFactory;
    public $fillable = [
        'subject',
        'description',
        'request_type',
        'document_id',
        'parent_id',
        'created_by',
        'processed_by',
        'processed_at',
        'request_status',
    ];

    public function createdBy()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }
    public function processed_by()
    {
        return $this->hasOne('App\Models\User', 'id', 'processed_by');
    }

    public function getStatusBadgeClassAttribute()
    {
        $statusClasses = [
            'Pending'    => 'warning',
            'Processing' => 'primary',
            'Approved'   => 'success',
            'Rejected'   => 'danger',
        ];

        return $statusClasses[$this->request_status] ?? 'secondary';
    }
}
