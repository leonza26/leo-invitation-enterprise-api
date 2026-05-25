<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'guest_name',
        'phone',
        'email',
        'address',
        'vip_status',
        'category',
        'seat_number',
        'attendance_status',
        'qr_code',
    ];

    protected $casts = [
        'vip_status' => 'boolean',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($guest) {
            if (empty($guest->qr_code)) {
                $guest->qr_code = Str::random(32);
            }
        });
    }

    /**
     * Get the event that owns the guest.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
