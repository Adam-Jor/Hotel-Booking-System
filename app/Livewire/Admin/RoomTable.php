<?php

namespace App\Livewire\Admin;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\ImageService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class RoomTable extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public ?int $hotelFilter = null;

    public bool $showModal = false;
    public ?int $editingId = null;
    public ?int $hotel_id = null;
    public ?int $room_type_id = null;
    public string $room_number = '';
    public string $price = '';
    public string $description = '';
    public string $status = 'available';
    public $img;
    public ?string $existingImg = null;

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function updatedHotelFilter(): void { $this->resetPage(); }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $room = Room::findOrFail($id);
        $this->editingId = $room->id;
        $this->hotel_id = $room->id_hotel;
        $this->room_type_id = $room->id_room_type;
        $this->room_number = $room->room_number;
        $this->price = (string) $room->price;
        $this->description = $room->description ?? '';
        $this->status = $room->status;
        $this->existingImg = $room->img_url;
        $this->showModal = true;
    }

    public function save(ImageService $images): void
    {
        $data = $this->validate([
            'hotel_id' => ['required', 'exists:tbl_hotels,id'],
            'room_type_id' => ['required', 'exists:tbl_room_types,id'],
            'room_number' => ['required', 'string', 'max:20'],
            'price' => ['required', 'numeric', 'min:10000', 'max:100000000'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:available,maintenance'],
            'img' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        unset($data['img']);

        if ($this->img) {
            $data['img_url'] = $images->store($this->img, 'rooms');
        }

        if ($this->editingId) {
            $room = Room::findOrFail($this->editingId);
            $room->update($data);
            $message = 'Room updated.';
        } else {
            $data['slug'] = \Illuminate\Support\Str::slug(
                (Hotel::find($this->hotel_id)?->name ?? 'room').'-'.$this->room_number.'-'.\Illuminate\Support\Str::random(4)
            );
            Room::create($data);
            $message = 'Room created.';
        }

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('toast', message: $message);
    }

    public function delete(int $id): void
    {
        Room::where('id', $id)->update(['archived' => 1]);
        $this->dispatch('toast', message: 'Room archived.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'hotel_id', 'room_type_id', 'room_number', 'price', 'description', 'status', 'img', 'existingImg']);
        $this->status = 'available';
        $this->resetErrorBag();
    }

    public function render()
    {
        $rooms = Room::with('hotel', 'roomType')
            ->where('archived', 0)
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('room_number', 'like', "%{$this->search}%")
                    ->orWhereHas('hotel', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->hotelFilter, fn ($q) => $q->where('id_hotel', $this->hotelFilter))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.room-table', [
            'rooms' => $rooms,
            'hotels' => Hotel::active()->orderBy('name')->get(),
            'types' => RoomType::where('archived', 0)->orderBy('name')->get(),
        ]);
    }
}
