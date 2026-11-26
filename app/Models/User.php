<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tbl_users';

    protected $fillable = [
        'name', 'username', 'email', 'password', 'phone', 'img_url',
        'role', 'status', 'last_login_time', 'archived',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_time' => 'datetime',
            'password' => 'hashed',
            'status' => 'integer',
            'archived' => 'boolean',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'id_user');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_user');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->img_url
            ? asset($this->img_url)
            : 'https://ui-avatars.com/api/?background=0194F3&color=fff&name='.urlencode($this->name);
    }

    public function setPhoneAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['phone'] = null;
            return;
        }
        $digits = preg_replace('/\D/', '', $value);
        // 08xxx -> 628xxx
        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }
        // 8xxx -> 628xxx
        if (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }
        // 62xxxx but not 628 -> assume 628...
        if (str_starts_with($digits, '62') && !str_starts_with($digits, '628')) {
            $digits = '628'.substr($digits, 2);
        }
        $this->attributes['phone'] = $digits;
    }
}
