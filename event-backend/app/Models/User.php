<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'phone'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean'
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    // Add relationship for event registrations (pivot table event_user)
    public function registrations()
    {
        return $this->belongsToMany(\App\Models\Event::class, 'event_user')->withPivot('status')->withTimestamps();
    }

    public function isAdmin()
    {
        return $this->is_admin === true;
    }
}