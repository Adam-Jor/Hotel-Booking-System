<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Hotel extends Model
{
    protected $table = 'tbl_hotels';

    protected $fillable = [
        'name', 'slug', 'city', 'address', 'latitude', 'longitude',
        'star_rating', 'description', 'img_url', 'status', 'archived',
    ];

    protected $casts = [
        'status' => 'integer',
        'archived' => 'boolean',
        'star_rating' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Hotel $hotel) {
            if (blank($hotel->slug)) {
                $hotel->slug = Str::slug($hotel->name);
            }
        });
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'id_hotel');
    }

    public function images()
    {
        return $this->hasMany(HotelImage::class, 'id_hotel')->orderBy('sort_order');
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'tbl_hotel_facilities', 'id_hotel', 'id_facility');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_hotel');
    }

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Room::class, 'id_hotel', 'id_room');
    }

    public function scopeActive($query)
    {
        return $query->where('archived', 0)->where('status', 1);
    }

    public function getImageAttribute(): string
    {
        return $this->img_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=70';
    }
}
