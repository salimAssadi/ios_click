<?php

namespace Modules\Tenant\Entities;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Modules\Tenant\Traits\BelongsToTenant;

class Permission extends SpatiePermission
{
    use BelongsToTenant;
    
    protected $table = 'permissions';
}
