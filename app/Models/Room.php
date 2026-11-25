<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Room extends Model
{
    protected $table = 'tbl_rooms';

    protected $fillable = [
        'id_hotel', 'id_room_type', 'room_number', 'slug',
        'price', 'description', 'img_url', 'status', 'archived',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'archived' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Room $room) {
            if (blank($room->slug)) {
                $base = Str::slug(($room->hotel?->name ?? 'room').'-'.$room->room_number);
                $room->slug = $base.'-'.Str::lower(Str::random(4));
            }
        });
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'id_room_type');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'id_room');
    }

    public function getImageAttribute(): string
    {
        return $this->img_url ?: 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1200&q=70';
    }
}
