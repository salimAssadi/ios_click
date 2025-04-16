<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Models\TenantModel;

class NoticeBoard extends TenantModel
{
    protected $fillable = [
        'title',
        'description',
        'attachment',
        'parent_id',
    ];
}
