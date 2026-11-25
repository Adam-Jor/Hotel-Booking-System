<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'tbl_reviews';

    protected $fillable = ['id_hotel', 'id_user', 'id_booking', 'rating', 'comment', 'archived'];

    protected $casts = ['archived' => 'boolean', 'rating' => 'integer'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking');
    }
}
