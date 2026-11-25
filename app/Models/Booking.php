<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'tbl_bookings';

    protected $fillable = [
        'booking_code', 'id_user', 'id_room', 'customer_name', 'guest_count',
        'check_in_date', 'check_out_date', 'total_nights', 'total_price',
        'status', 'notes', 'price_per_night', 'expires_at', 'cancelled_at',
        'cancel_reason', 'archived',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'archived' => 'boolean',
        'total_price' => 'decimal:0',
        'price_per_night' => 'decimal:0',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'id_room');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'id_booking');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'id_booking');
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class, 'id_booking')->latestOfMany();
    }

    public function scopeActive($query)
    {
        return $query->where('archived', 0);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->isPending() && $this->expires_at && $this->expires_at->isPast();
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'green',
            'pending' => 'amber',
            'completed' => 'blue',
            'cancelled' => 'red',
            default => 'slate',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }
}
