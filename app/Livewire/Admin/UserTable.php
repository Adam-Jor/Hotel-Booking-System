<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UserTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $role = '';

    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $formRole = 'customer';
    public int $status = 1;

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedRole(): void { $this->resetPage(); }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->formRole = $user->role;
        $this->status = $user->status;
        $this->password = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('tbl_users', 'username')->ignore($this->editingId)],
            'email' => ['required', 'email', 'max:160', Rule::unique('tbl_users', 'email')->ignore($this->editingId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:8'],
            'formRole' => ['required', 'in:admin,customer'],
            'status' => ['required', 'in:0,1'],
        ]);

        $data['role'] = $data['formRole'];
        unset($data['formRole']);

        if (blank($this->password)) {
            unset($data['password']);
        }

        if ($this->editingId) {
            User::findOrFail($this->editingId)->update($data);
            $message = 'User updated.';
        } else {
            User::create($data);
            $message = 'User created.';
        }

        $this->showModal = false;
        $this->resetForm();
        session()->flash('success', $message);
    }

    public function toggleStatus(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('success', 'You cannot deactivate your own account.');

            return;
        }

        $user = User::findOrFail($id);
        $user->update(['status' => $user->status ? 0 : 1]);
        session()->flash('success', "{$user->name} is now ".($user->status ? 'active' : 'inactive').'.');
    }

    public function archive(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('success', 'You cannot archive your own account.');

            return;
        }

        User::where('id', $id)->update(['archived' => 1, 'status' => 0]);
        session()->flash('success', 'User archived.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'username', 'email', 'phone', 'password', 'formRole', 'status']);
        $this->formRole = 'customer';
        $this->status = 1;
        $this->resetErrorBag();
    }

    public function render()
    {
        $users = User::where('archived', 0)
            ->withCount('bookings')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('username', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%");
            }))
            ->when($this->role, fn ($q) => $q->where('role', $this->role))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-table', compact('users'));
    }
}
