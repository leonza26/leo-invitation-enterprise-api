<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    /**
     * Find user by ID with their role and permissions.
     */
    public function find(int $id): ?User
    {
        return User::with('role.permissions')->find($id);
    }

    /**
     * Find user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Get all users belonging to a specific role slug.
     */
    public function getByRole(string $roleSlug): Collection
    {
        return User::whereHas('role', function ($query) use ($roleSlug) {
            $query->where('slug', $roleSlug);
        })->get();
    }
}
