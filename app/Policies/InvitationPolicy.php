<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invitation;
use Illuminate\Auth\Access\Response;

class InvitationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Super Admin and Operator can see all invitations. Clients can see theirs. Guests can see their own.
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invitation $invitation): bool
    {
        if ($user->hasRole('super_admin') || $user->hasRole('operator')) {
            return true;
        }

        return $user->id === $invitation->client_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('client');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invitation $invitation): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasRole('client') && $user->id === $invitation->client_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invitation $invitation): bool
    {
        // Only Super Admins can physically delete invitation instances.
        return $user->hasRole('super_admin');
    }
}
