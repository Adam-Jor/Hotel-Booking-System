<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $table = 'tbl_facilities';

    protected $fillable = ['name', 'icon', 'archived'];

    protected $casts = ['archived' => 'boolean'];

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'tbl_hotel_facilities', 'id_facility', 'id_hotel');
    }
}
