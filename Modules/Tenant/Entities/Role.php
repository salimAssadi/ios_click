<?php

namespace Modules\Tenant\Entities;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\BelongsToTenant;

class Role extends SpatieRole
{
    use BelongsToTenant;
    
    protected $table = 'roles';
}
