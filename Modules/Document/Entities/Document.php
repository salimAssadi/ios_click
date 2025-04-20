<?php

namespace Modules\Document\Entities;

use App\Models\BaseModel;
use Modules\Iso\Entities\IsoSystem;
use Modules\Document\Entities\DocumentVersion;
use Modules\Tenant\Entities\User;
class Document extends BaseModel

{
    protected $fillable = [
        'title', 'document_number', 'document_type', 'related_process', 'department',
        'owner', 'created_by', 'creation_date', 'is_obsolete', 'obsoleted_date', 'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
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
        return $query->whereHas('lastVersion', function ($q) use ($status) {
            $q->where('status', $status);
        });
    }

    public function scopeByType($query, $documentType)
    {
        return $query->where('document_type', $documentType);
    }

}
