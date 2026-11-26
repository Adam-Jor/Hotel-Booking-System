<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\WhatsappLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsappService
{
    public function send(Booking $booking, string $event): void
    {
        $booking->loadMissing('user', 'room.hotel', 'room.roomType');
        $phone = $this->normalizePhone($booking->user?->phone);

        if (! $phone) {
            return;
        }

        $message = $this->message($booking, $event);

        $log = WhatsappLog::create([
            'id_booking' => $booking->id,
            'phone' => $phone,
            'message' => $message,
            'status' => 'queued',
        ]);

        $token = config('booking.whatsapp_token');

        if (! $token) {
            $log->update(['status' => 'logged', 'response' => 'No FONNTE_TOKEN configured; message logged only.']);

            return;
        }

        try {
            $response = Http::withHeaders(['Authorization' => $token])
                ->timeout(15)
                ->post('https://api.fonnte.com/send', [
                    'target' => $phone,
                    'message' => $message,
                ]);

            $body = $response->json();
            $fonnteStatus = data_get($body, 'status', $response->successful());

            $log->update([
                'status' => $fonnteStatus ? 'sent' : 'failed',
                'response' => Str::limit($response->body(), 900),
            ]);

            if (! $fonnteStatus) {
                throw new \RuntimeException('Fonnte rejected: '.($body['reason'] ?? $response->body()));
            }
        } catch (\Throwable $e) {
            $log->update(['status' => 'failed', 'response' => Str::limit($e->getMessage(), 900)]);
            Log::warning('WhatsApp send failed', ['booking' => $booking->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function message(Booking $booking, string $event): string
    {
        $hotel = $booking->room?->hotel?->name ?? 'NusaStay';
        $type = $booking->room?->roomType?->name ?? 'Room';
        $checkIn = $booking->check_in_date->format('j M Y');
        $total = 'Rp '.number_format((float) $booking->total_price, 0, ',', '.');

        return match ($event) {
            'paid' => "Halo {$booking->customer_name}, pembayaran booking {$booking->booking_code} di {$hotel} ({$type}) sudah kami terima. Check-in: {$checkIn}. Total: {$total}. Invoice tersedia di halaman Pesanan Saya.",
            'rejected' => "Halo {$booking->customer_name}, bukti pembayaran booking {$booking->booking_code} belum dapat kami verifikasi. Silakan unggah ulang sebelum batas waktu. Terima kasih.",
            'cancelled' => "Halo {$booking->customer_name}, booking {$booking->booking_code} di {$hotel} telah dibatalkan. Kamar sudah dilepas.",
            'expired' => "Halo {$booking->customer_name}, booking {$booking->booking_code} di {$hotel} kedaluwarsa karena belum dibayar. Silakan buat booking baru.",
            'reminder' => "Halo {$booking->customer_name}, pengingat check-in besok di {$hotel} ({$type}). Kode booking: {$booking->booking_code}. Sampai jumpa.",
            'checkin' => "Halo {$booking->customer_name}, selamat datang! Check-in booking {$booking->booking_code} di {$hotel} ({$type}) hari ini ({$checkIn}). Selamat menginap!",
            default => "Halo {$booking->customer_name}, booking {$booking->booking_code} di {$hotel} ({$type}) berhasil dibuat. Check-in: {$checkIn}. Total: {$total}. Status: {$booking->status_label}. Bayar sebelum ".optional($booking->expires_at)->format('j M Y H:i').' WIB.',
        };
    }

    private function normalizePhone(?string $phone): ?string
    {
        if ($phone === null || $phone === '') return null;
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '0')) $digits = '62'.substr($digits, 1);
        if (str_starts_with($digits, '8')) $digits = '62'.$digits;
        if (str_starts_with($digits, '62') && !str_starts_with($digits, '628')) {
            $digits = '628'.substr($digits, 2);
        }
        return $digits;
    }
}
