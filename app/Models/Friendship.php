<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;

class Friendship extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
    ];

    // Sender user
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Receiver user
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Scope for accepted friendships
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    // Scope for pending friendships
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

}
