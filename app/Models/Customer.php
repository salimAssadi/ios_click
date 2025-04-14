<?php

namespace App\Models;

use Modules\Tenant\Models\TenantModel;

class Customer extends TenantModel
{
    protected $fillable = [
        'name',
        'email',
        // ... other fields
    ];
}
