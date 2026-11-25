<div>
    <div class="rounded-card border border-line bg-white">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line p-4">
            <div class="flex flex-wrap items-center gap-2">
                <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search room or hotel…" class="field w-56">
                <select wire:model.live="hotelFilter" class="field w-44">
                    <option value="">All hotels</option>
                    @foreach ($hotels as $hotel)
                        <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="statusFilter" class="field w-40">
                    <option value="">All status</option>
                    <option value="available">Available</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <button type="button" wire:click="create" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add room</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface">
                    <tr>
                        <th class="table-th">Room</th>
                        <th class="table-th">Hotel</th>
                        <th class="table-th">Type</th>
                        <th class="table-th">Price / night</th>
                        <th class="table-th">Status</th>
                        <th class="table-th text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($rooms as $room)
                        <tr class="hover:bg-surface/60">
                            <td class="table-td">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $room->image }}" alt="" class="h-10 w-14 rounded object-cover">
                                    <span class="font-semibold">{{ $room->room_number }}</span>
                                </div>
                            </td>
                            <td class="table-td">{{ $room->hotel?->name }}</td>
                            <td class="table-td">{{ $room->roomType?->name }} · {{ $room->roomType?->capacity }} guests</td>
                            <td class="table-td font-semibold">Rp {{ number_format((float) $room->price, 0, ',', '.') }}</td>
                            <td class="table-td">
                                <span class="badge {{ $room->status === 'available' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst($room->status) }}</span>
                            </td>
                            <td class="table-td">
                                <div class="flex justify-end gap-1">
                                    <button type="button" wire:click="edit({{ $room->id }})" class="rounded p-1.5 text-muted hover:bg-surface hover:text-brand-600"><x-icon name="pencil" class="h-4 w-4" /></button>
                                    <button type="button" wire:click="delete({{ $room->id }})" wire:confirm="Archive this room?" class="rounded p-1.5 text-muted hover:bg-red-50 hover:text-red-600"><x-icon name="trash" class="h-4 w-4" /></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-muted">No rooms found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line p-4">{{ $rooms->links() }}</div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-ink/40 p-4 sm:items-center">
            <div class="w-full max-w-lg rounded-card bg-white shadow-float">
                <div class="flex items-center justify-between border-b border-line px-5 py-4">
                    <h2 class="text-base font-bold">{{ $editingId ? 'Edit room' : 'Add room' }}</h2>
                    <button type="button" wire:click="$set('showModal', false)" class="rounded p-1 text-muted hover:bg-surface"><x-icon name="x" class="h-5 w-5" /></button>
                </div>
                <form wire:submit="save" class="max-h-[70vh] space-y-4 overflow-y-auto p-5">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label">Hotel <span class="text-accent">*</span></label>
                            <select wire:model="hotel_id" class="field">
                                <option value="">Select hotel</option>
                                @foreach ($hotels as $hotel)
                                    <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                                @endforeach
                            </select>
                            @error('hotel_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label">Room type <span class="text-accent">*</span></label>
                            <select wire:model="room_type_id" class="field">
                                <option value="">Select type</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('room_type_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label">Room number <span class="text-accent">*</span></label>
                            <input wire:model="room_number" type="text" placeholder="101" class="field">
                            @error('room_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label">Price / night <span class="text-accent">*</span></label>
                            <input wire:model="price" type="number" min="10000" step="10000" placeholder="850000" class="field">
                            @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="label">Description</label>
                        <textarea wire:model="description" rows="2" class="field"></textarea>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label">Photo</label>
                            <input type="file" wire:model="img" accept="image/*" class="field py-2">
                            @error('img') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label">Status</label>
                            <select wire:model="status" class="field">
                                <option value="available">Available</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                    @if ($img || $existingImg)
                        <img src="{{ $img ? $img->temporaryUrl() : asset($existingImg) }}" alt="Preview" class="h-28 w-full rounded-control object-cover">
                    @endif
                    <div class="flex justify-end gap-2 border-t border-line pt-4">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-ghost border border-line">Cancel</button>
                        <button type="submit" class="btn-primary">{{ $editingId ? 'Save changes' : 'Create room' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
