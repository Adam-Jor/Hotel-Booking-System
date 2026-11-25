<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $table = 'tbl_whatsapp_logs';

    protected $fillable = ['id_booking', 'phone', 'message', 'status', 'response'];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking');
    }
}
