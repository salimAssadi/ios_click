<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Tenant\Models\TenantModel;

class Tag extends TenantModel
{
    use HasFactory;
    public $fillable=[
        'title',
        'parent_id',
    ];
}
