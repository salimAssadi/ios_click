<?php

namespace Modules\Role\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Role\Entities\User;
use Spatie\Permission\Models\Permission;
use App\Models\BaseModel;

class Role extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'guard_name',
    ];
    
    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }
    
    /**
     * The permissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }
    
    protected static function newFactory()
    {
        return \Modules\Role\Database\factories\RoleFactory::new();
    }
}
