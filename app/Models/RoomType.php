<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RoomType extends Model
{
    protected $table = 'tbl_room_types';

    protected $fillable = ['name', 'slug', 'description', 'capacity', 'archived'];

    protected $casts = ['archived' => 'boolean', 'capacity' => 'integer'];

    protected static function booted(): void
    {
        static::saving(function (RoomType $type) {
            if (blank($type->slug)) {
                $type->slug = Str::slug($type->name);
            }
        });
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'id_room_type');
    }
}
