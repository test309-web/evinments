<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'location',
        'address',
        'category',
        'available_tickets',
        'price',
        'user_id',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_user')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function approvedAttendees()
    {
        return $this->attendees()->wherePivot('status', 'approved');
    }

    public function getAvailableTicketsCountAttribute()
    {
        return $this->available_tickets - $this->approvedAttendees()->count();
    }
}