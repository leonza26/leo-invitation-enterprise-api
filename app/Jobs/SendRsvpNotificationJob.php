<?php

namespace App\Jobs;

use App\Models\Rsvp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendRsvpNotificationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Rsvp $rsvp)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Log sending email notifications to the invitation owner
        \Log::info("Sending RSVP notification for guest {$this->rsvp->guest_name} on invitation ID {$this->rsvp->invitation_id}");
    }
}
