<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Component;

class QuickBookingForm extends Component
{
    public string $customer_name = '';
    public ?int $hotel_id = null;
    public ?int $room_id = null;
    public string $check_in = '';
    public string $check_out = '';

    public function mount(): void
    {
        $this->check_in = Carbon::today()->toDateString();
        $this->check_out = Carbon::tomorrow()->toDateString();
    }

    public function save()
    {
        $this->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'hotel_id' => ['required', 'exists:tbl_hotels,id'],
            'room_id' => ['required', 'exists:tbl_rooms,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
        ]);

        $room = Room::where('id_hotel', $this->hotel_id)->findOrFail($this->room_id);
        $nights = Carbon::parse($this->check_in)->diffInDays(Carbon::parse($this->check_out));

        $user = User::where('role', 'customer')->first()
            ?? User::create([
                'name' => $this->customer_name,
                'username' => 'walkin'.time(),
                'email' => 'walkin'.time().'@hotel.test',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'status' => 1,
            ]);

        Booking::create([
            'booking_code' => 'BKG-'.str_pad((string) (Booking::max('id') + 1), 3, '0', STR_PAD_LEFT),
            'id_user' => $user->id,
            'id_room' => $room->id,
            'customer_name' => $this->customer_name,
            'guest_count' => 1,
            'check_in_date' => $this->check_in,
            'check_out_date' => $this->check_out,
            'total_nights' => $nights,
            'price_per_night' => $room->price,
            'total_price' => (float) $room->price * $nights,
            'status' => 'draft',
        ]);

        $this->reset(['customer_name', 'hotel_id', 'room_id']);
        $this->check_in = Carbon::today()->toDateString();
        $this->check_out = Carbon::tomorrow()->toDateString();

        $this->dispatch('booking-created');
        $this->dispatch('toast', message: 'Draft booking created.');
    }

    public function render()
    {
        return view('livewire.admin.quick-booking-form', [
            'hotels' => Hotel::active()->orderBy('name')->get(),
            'rooms' => $this->hotel_id
                ? Room::where('id_hotel', $this->hotel_id)->where('archived', 0)->with('roomType')->orderBy('room_number')->get()
                : collect(),
        ]);
    }
}
