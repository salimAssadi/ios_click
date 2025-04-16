<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Models\TenantModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoPolicyArchive extends TenantModel
{
    use HasFactory;

    protected $connection = 'iso_dic';

    protected $fillable = [
        'policy_id',
        'version',
        'content',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'archived_by',
        'reason'
    ];

    protected $casts = [
        'archived_at' => 'datetime'
    ];

    public function policy()
    {
        return $this->belongsTo(IsoPolicy::class);
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function attachments()
    {
        return $this->hasMany(IsoPolicyArchiveAttachment::class, 'archive_id');
    }
}
