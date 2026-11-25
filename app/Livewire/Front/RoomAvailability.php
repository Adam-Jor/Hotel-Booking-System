<?php

namespace App\Livewire\Front;

use App\Models\Hotel;
use App\Services\AvailabilityService;
use Illuminate\Support\Carbon;
use Livewire\Component;

class RoomAvailability extends Component
{
    public int $hotelId;
    public string $check_in = '';
    public string $check_out = '';
    public int $guests = 2;

    public function mount(int $hotelId): void
    {
        $this->hotelId = $hotelId;
        $this->check_in = (string) request('check_in', Carbon::today()->toDateString());
        $this->check_out = (string) request('check_out', Carbon::tomorrow()->toDateString());
        $this->guests = (int) request('guests', 2);
    }

    public function updated($property): void
    {
        if (in_array($property, ['check_in', 'check_out'], true)) {
            if (Carbon::parse($this->check_out)->lte(Carbon::parse($this->check_in))) {
                $this->check_out = Carbon::parse($this->check_in)->addDay()->toDateString();
            }
        }
    }

    public function render(AvailabilityService $availability)
    {
        $hotel = Hotel::findOrFail($this->hotelId);
        $nights = max(1, Carbon::parse($this->check_in)->diffInDays(Carbon::parse($this->check_out)));

        $rooms = $availability->availableRoomsQuery($hotel->id, $this->check_in, $this->check_out)
            ->with('roomType')
            ->orderBy('price')
            ->get();

        return view('livewire.front.room-availability', [
            'rooms' => $rooms,
            'nights' => $nights,
            'hotel' => $hotel,
        ]);
    }
}
