<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'event_name',
        'slug',
        'event_type',
        'theme',
        'venue',
        'event_date',
        'description',
        'status',
        'package'
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->event_name) . '-' . Str::random(5);
            }
        });
    }
}
