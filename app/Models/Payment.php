<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'tbl_payments';

    protected $fillable = [
        'id_booking', 'payment_method', 'amount', 'payment_date', 'proof_url',
        'reference_code', 'verified_by', 'verified_at', 'reject_reason', 'status', 'archived',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'verified_at' => 'datetime',
        'archived' => 'boolean',
        'amount' => 'decimal:0',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'bank_transfer' => 'Bank Transfer',
            'ewallet_sim' => 'E-Wallet',
            'cash' => 'Cash',
            default => $this->payment_method,
        };
    }
}
