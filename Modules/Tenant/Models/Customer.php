<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Models\TenantModel;

class Customer extends TenantModel
{

    protected $fillable = [
        'name',
        'email',
        // ... other fields
    ];
}
