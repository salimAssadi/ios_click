<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Tenant\Models\TenantModel;

class IsoPolicy extends TenantModel
{
    use HasFactory, Localizable;

    protected $connection = 'iso_dic';

   
    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'content',
        'version',
        'status',
        'approved_at',
        'approved_by',
        'is_published'
    ];


    protected $casts = [
        'is_published' => 'boolean',
        'approved_at' => 'datetime'
    ];

    public function getNameAttribute()
    {
        return $this->getLocalizedAttribute('name');
    }

    public function getDescriptionAttribute()
    {
        return $this->getLocalizedAttribute('description');
    }

    public function attachments()
    {
        return $this->hasMany(IsoPolicyAttachment::class, 'policy_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function versions()
    {
        return $this->hasMany(IsoPolicyVersion::class, 'policy_id');
    }

    public function archives()
    {
        return $this->hasMany(IsoPolicyArchive::class, 'policy_id');
    }

    public function googleDoc()
    {
        return $this->hasOne(GoogleDocMapping::class, 'document_id')->where('document_type', 'policy');
    }

    public function getStatusBadgeAttribute()
    {
        $badgeClasses = [
            'draft' => 'badge bg-secondary',
            'pending' => 'badge bg-warning text-dark',
            'approved' => 'badge bg-success',
            'rejected' => 'badge bg-danger',
        ];
        $status = strtolower($this->attributes['status']);
        $badgeClass = $badgeClasses[$status] ?? 'badge bg-info';

        return '<span class="' . $badgeClass . '">' . ucfirst($status) . '</span>';
    }
}
