<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Models\TenantModel;

class Department extends TenantModel
{
    protected $fillable = [
        'name',
    ];
}
