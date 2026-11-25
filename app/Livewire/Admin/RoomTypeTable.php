<?php

namespace App\Livewire\Admin;

use App\Models\RoomType;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class RoomTypeTable extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public int $capacity = 2;
    public string $description = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $type = RoomType::findOrFail($id);
        $this->editingId = $type->id;
        $this->name = $type->name;
        $this->capacity = $type->capacity;
        $this->description = $type->description ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:80'],
            'capacity' => ['required', 'integer', 'min:1', 'max:10'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['slug'] = Str::slug($this->name);

        if ($this->editingId) {
            RoomType::findOrFail($this->editingId)->update($data);
            $message = 'Room type updated.';
        } else {
            RoomType::create($data);
            $message = 'Room type created.';
        }

        $this->showModal = false;
        $this->resetForm();
        session()->flash('success', $message);
    }

    public function delete(int $id): void
    {
        RoomType::where('id', $id)->update(['archived' => 1]);
        session()->flash('success', 'Room type archived.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'capacity', 'description']);
        $this->capacity = 2;
        $this->resetErrorBag();
    }

    public function render()
    {
        $types = RoomType::withCount('rooms')
            ->where('archived', 0)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.room-type-table', compact('types'));
    }
}
