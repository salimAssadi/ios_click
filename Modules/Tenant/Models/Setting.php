<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Models\TenantModel;

class Setting extends TenantModel
{
    protected $fillable = [
        'name',
        'value',
        'type',
        'parent_id',

    ];
}
