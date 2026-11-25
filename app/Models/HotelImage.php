<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelImage extends Model
{
    protected $table = 'tbl_hotel_images';

    protected $fillable = ['id_hotel', 'img_url', 'sort_order', 'archived'];

    protected $casts = ['archived' => 'boolean'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel');
    }
}
