<?php

namespace App\Observers;

use App\Models\Invitation;
use Illuminate\Support\Str;

class InvitationObserver
{
    /**
     * Handle the Invitation "creating" event.
     */
    public function creating(Invitation $invitation): void
    {
        if (empty($invitation->slug)) {
            $invitation->slug = Str::slug($invitation->groom_name . '-' . $invitation->bride_name);
        } else {
            $invitation->slug = Str::slug($invitation->slug);
        }
    }

    /**
     * Handle the Invitation "updating" event.
     */
    public function updating(Invitation $invitation): void
    {
        if ($invitation->isDirty('slug')) {
            $invitation->slug = Str::slug($invitation->slug);
        }
    }
}
