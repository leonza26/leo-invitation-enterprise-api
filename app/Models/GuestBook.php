<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'guest_name',
        'message',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    /**
     * The event this guest book comment belongs to.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
