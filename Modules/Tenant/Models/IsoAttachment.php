<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Models\TenantModel;

class IsoAttachment extends TenantModel
{
    use HasFactory;
    protected $table = 'iso_attachments';

    protected $fillable = [
        'iso_system_id',
        'name_ar',
        'name_en',
        'type',
        'file_path',
        'is_published'
    ];

    public function isoSystem()
    {
        return $this->belongsTo(IsoSystem::class);
    }
}
