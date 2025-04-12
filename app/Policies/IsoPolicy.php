<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IsoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view-iso');
    }

    public function view(User $user)
    {
        return $user->hasPermissionTo('view-iso');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create-iso');
    }

    public function update(User $user)
    {
        return $user->hasPermissionTo('edit-iso');
    }

    public function delete(User $user)
    {
        return $user->hasPermissionTo('delete-iso');
    }

    public function manageAttachments(User $user)
    {
        return $user->hasPermissionTo('manage-iso-attachments');
    }

    public function publish(User $user)
    {
        return $user->hasPermissionTo('publish-iso');
    }
}
