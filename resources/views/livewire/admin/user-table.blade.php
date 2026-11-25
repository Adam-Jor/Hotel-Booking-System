<div>
    <div class="rounded-card border border-line bg-white">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line p-4">
            <div class="flex flex-wrap items-center gap-2">
                <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search name, email or phone…" class="field w-64">
                <select wire:model.live="role" class="field w-36">
                    <option value="">All roles</option>
                    <option value="admin">Admin</option>
                    <option value="customer">Customer</option>
                </select>
            </div>
            <button type="button" wire:click="create" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add user</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface">
                    <tr>
                        <th class="table-th">User</th>
                        <th class="table-th">Contact</th>
                        <th class="table-th">Role</th>
                        <th class="table-th">Bookings</th>
                        <th class="table-th">Last login</th>
                        <th class="table-th">Status</th>
                        <th class="table-th text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($users as $user)
                        <tr class="hover:bg-surface/60">
                            <td class="table-td">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar_url }}" alt="" class="h-9 w-9 rounded-full object-cover">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold">{{ $user->name }}</p>
                                        <p class="truncate text-xs text-muted">{{ '@'.$user->username }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="table-td">
                                <p>{{ $user->email }}</p>
                                <p class="text-xs text-muted">{{ $user->phone ?: 'No phone' }}</p>
                            </td>
                            <td class="table-td">
                                <span class="badge {{ $user->role === 'admin' ? 'bg-brand-50 text-brand-700' : 'bg-surface text-muted' }}">{{ ucfirst($user->role) }}</span>
                            </td>
                            <td class="table-td">{{ $user->bookings_count }}</td>
                            <td class="table-td whitespace-nowrap text-muted">{{ $user->last_login_time?->diffForHumans() ?? 'Never' }}</td>
                            <td class="table-td">
                                <span class="badge {{ $user->status ? 'bg-emerald-50 text-emerald-700' : 'bg-surface text-muted' }}">{{ $user->status ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="table-td">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" wire:click="edit({{ $user->id }})" class="rounded p-1.5 text-muted hover:bg-surface hover:text-brand-600" title="Edit">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </button>
                                    <button type="button" wire:click="toggleStatus({{ $user->id }})" class="rounded p-1.5 text-muted hover:bg-surface hover:text-brand-600" title="{{ $user->status ? 'Deactivate' : 'Activate' }}">
                                        <x-icon name="{{ $user->status ? 'x' : 'check' }}" class="h-4 w-4" />
                                    </button>
                                    <button type="button" wire:click="archive({{ $user->id }})" wire:confirm="Archive this user? Their booking history stays intact." class="rounded p-1.5 text-muted hover:bg-red-50 hover:text-red-600" title="Archive">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-muted">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line p-4">{{ $users->links() }}</div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-ink/40 p-4 sm:items-center">
            <div class="w-full max-w-lg rounded-card bg-white shadow-float">
                <div class="flex items-center justify-between border-b border-line px-5 py-4">
                    <h2 class="text-base font-bold">{{ $editingId ? 'Edit user' : 'Add user' }}</h2>
                    <button type="button" wire:click="$set('showModal', false)" class="rounded p-1 text-muted hover:bg-surface"><x-icon name="x" class="h-5 w-5" /></button>
                </div>
                <form wire:submit="save" class="space-y-4 p-5">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label">Full name <span class="text-accent">*</span></label>
                            <input wire:model="name" type="text" class="field">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label">Username <span class="text-accent">*</span></label>
                            <input wire:model="username" type="text" class="field">
                            @error('username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label">Email <span class="text-accent">*</span></label>
                            <input wire:model="email" type="email" class="field">
                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label">Phone</label>
                            <input wire:model="phone" type="text" placeholder="628…" class="field">
                            @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div>
                            <label class="label">Role</label>
                            <select wire:model="formRole" class="field">
                                <option value="customer">Customer</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Status</label>
                            <select wire:model="status" class="field">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Password</label>
                            <input wire:model="password" type="password" placeholder="{{ $editingId ? 'Keep current' : 'Min. 8 chars' }}" class="field">
                            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-line pt-4">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-ghost border border-line">Cancel</button>
                        <button type="submit" class="btn-primary">{{ $editingId ? 'Save changes' : 'Create user' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
