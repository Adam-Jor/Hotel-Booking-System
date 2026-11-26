<?php

namespace App\Livewire\Admin;

use App\Models\Hotel;
use App\Services\ImageService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class HotelTable extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';
    public string $sortBy = 'name';
    public string $sortDir = 'asc';

    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $city = '';
    public string $address = '';
    public ?int $star_rating = 4;
    public string $description = '';
    public int $status = 1;
    public $img;
    public ?string $existingImg = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $hotel = Hotel::findOrFail($id);
        $this->editingId = $hotel->id;
        $this->name = $hotel->name;
        $this->city = $hotel->city;
        $this->address = $hotel->address;
        $this->star_rating = $hotel->star_rating;
        $this->description = $hotel->description;
        $this->status = $hotel->status;
        $this->existingImg = $hotel->img_url;
        $this->showModal = true;
    }

    public function save(ImageService $images): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:80'],
            'address' => ['required', 'string', 'max:255'],
            'star_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'description' => ['required', 'string', 'max:2000'],
            'status' => ['required', 'in:0,1'],
            'img' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        unset($data['img']);

        if ($this->img) {
            $data['img_url'] = $images->store($this->img, 'hotels');
        }

        if ($this->editingId) {
            $hotel = Hotel::findOrFail($this->editingId);
            $data['slug'] = \Illuminate\Support\Str::slug($this->name);
            $hotel->update($data);
            $message = 'Hotel updated.';
        } else {
            $data['slug'] = \Illuminate\Support\Str::slug($this->name);
            Hotel::create($data);
            $message = 'Hotel created.';
        }

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('toast', message: $message);
    }

    public function delete(int $id): void
    {
        Hotel::where('id', $id)->update(['archived' => 1]);
        $this->dispatch('toast', message: 'Hotel archived.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'city', 'address', 'star_rating', 'description', 'status', 'img', 'existingImg']);
        $this->star_rating = 4;
        $this->status = 1;
        $this->resetErrorBag();
    }

    public function render()
    {
        $hotels = Hotel::withCount('rooms')
            ->where('archived', 0)
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")->orWhere('city', 'like', "%{$this->search}%");
            }))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(10);

        return view('livewire.admin.hotel-table', compact('hotels'));
    }
}
