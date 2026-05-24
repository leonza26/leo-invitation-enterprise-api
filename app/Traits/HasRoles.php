<?php

namespace App\Traits;

use App\Models\Role;

trait HasRoles
{
    /**
     * Relationship to the Role model.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Determine if the user has a specific role.
     *
     * @param string|array $role Slug of the role(s) to check.
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            return in_array($this->role?->slug, $role);
        }

        return $this->role?->slug === $role;
    }

    /**
     * Determine if the user has a specific permission.
     *
     * @param string $permission Slug of the permission to check.
     * @return bool
     */
    public function hasPermission(string $permission)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Determine if the user is a super admin.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('super_admin');
    }
}
