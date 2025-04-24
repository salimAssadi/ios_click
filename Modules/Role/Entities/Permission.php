<?php

namespace Modules\Role\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Traits\BelongsToTenant;

class Permission extends SpatiePermission
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'name',
        'description',
        'guard_name',
    ];
    
    /**
     * The roles that belong to the permission.
     */
    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id', 'role_id');
    // }
    
    protected static function newFactory()
    {
        return \Modules\Role\Database\factories\PermissionFactory::new();
    }
}
