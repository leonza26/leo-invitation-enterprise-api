<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'slug',
        'title',
        'groom_name',
        'bride_name',
        'wedding_date',
        'wedding_location',
        'theme_config',
        'status',
    ];

    protected $casts = [
        'wedding_date' => 'datetime',
        'theme_config' => 'array',
    ];

    /**
     * The client who owns this invitation.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * RSVPs received for this invitation.
     */
    public function rsvps()
    {
        return $this->hasMany(Rsvp::class);
    }

    /**
     * Guest book messages/wishes left for this invitation.
     */
    public function guestBooks()
    {
        return $this->hasMany(GuestBook::class);
    }
}
