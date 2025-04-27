<?php

namespace Modules\Role\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Role\Entities\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\BaseModel;
use App\Traits\BelongsToTenant;

class Role extends SpatieRole
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'name',
        'guard_name',
        'module',
    ];
    
    /**
     * The users that belong to the role.
     */
   
    /**
     * The permissions that belong to the role.
     */
    // public function permissions()
    // {
    //     return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    // }
    
    protected static function newFactory()
    {
        return \Modules\Role\Database\factories\RoleFactory::new();
    }
}






