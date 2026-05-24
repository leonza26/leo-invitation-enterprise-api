<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class RbacService
{
    /**
     * Assign a role to a user.
     */
    public function assignRoleToUser(User $user, string $roleSlug): bool
    {
        $role = Role::where('slug', $roleSlug)->first();
        if (!$role) {
            return false;
        }

        $user->role_id = $role->id;
        return $user->save();
    }

    /**
     * Attach a permission to a role.
     */
    public function attachPermissionToRole(string $roleSlug, string $permissionSlug): bool
    {
        $role = Role::where('slug', $roleSlug)->first();
        $permission = Permission::where('slug', $permissionSlug)->first();

        if (!$role || !$permission) {
            return false;
        }

        if (!$role->permissions()->where('slug', $permissionSlug)->exists()) {
            $role->permissions()->attach($permission->id);
        }

        return true;
    }
}
