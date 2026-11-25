<div>
    <div class="rounded-card border border-line bg-white">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line p-4">
            <div class="relative w-full max-w-xs">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" />
                <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search name or city…" class="field pl-9">
            </div>
            <button type="button" wire:click="create" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4" /> Add hotel
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface">
                    <tr>
                        <th class="table-th">Hotel</th>
                        <th class="table-th cursor-pointer" wire:click="sort('city')">City</th>
                        <th class="table-th cursor-pointer" wire:click="sort('star_rating')">Stars</th>
                        <th class="table-th">Rooms</th>
                        <th class="table-th">Status</th>
                        <th class="table-th text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($hotels as $hotel)
                        <tr class="hover:bg-surface/60">
                            <td class="table-td">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $hotel->image }}" alt="" class="h-11 w-16 rounded object-cover">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold">{{ $hotel->name }}</p>
                                        <p class="truncate text-xs text-muted">{{ $hotel->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="table-td">{{ $hotel->city }}</td>
                            <td class="table-td">
                                <span class="inline-flex items-center gap-0.5 text-accent">
                                    @for ($i = 0; $i < $hotel->star_rating; $i++)
                                        <x-icon name="star" class="h-3.5 w-3.5 fill-accent text-accent" />
                                    @endfor
                                </span>
                            </td>
                            <td class="table-td">{{ $hotel->rooms_count }}</td>
                            <td class="table-td">
                                <span class="badge {{ $hotel->status ? 'bg-emerald-50 text-emerald-700' : 'bg-surface text-muted' }}">{{ $hotel->status ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="table-td">
                                <div class="flex justify-end gap-1">
                                    <button type="button" wire:click="edit({{ $hotel->id }})" class="rounded p-1.5 text-muted hover:bg-surface hover:text-brand-600" title="Edit">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </button>
                                    <button type="button" wire:click="delete({{ $hotel->id }})" wire:confirm="Archive this hotel?" class="rounded p-1.5 text-muted hover:bg-red-50 hover:text-red-600" title="Archive">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-muted">No hotels found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-line p-4">{{ $hotels->links() }}</div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-ink/40 p-4 sm:items-center">
            <div class="w-full max-w-xl rounded-card bg-white shadow-float">
                <div class="flex items-center justify-between border-b border-line px-5 py-4">
                    <h2 class="text-base font-bold">{{ $editingId ? 'Edit hotel' : 'Add hotel' }}</h2>
                    <button type="button" wire:click="$set('showModal', false)" class="rounded p-1 text-muted hover:bg-surface"><x-icon name="x" class="h-5 w-5" /></button>
                </div>

                <form wire:submit="save" class="max-h-[70vh] space-y-4 overflow-y-auto p-5">
                    <div>
                        <label class="label">Hotel name <span class="text-accent">*</span></label>
                        <input wire:model="name" type="text" placeholder="Grand Aston Bali Beach Resort" class="field" autofocus>
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label">City <span class="text-accent">*</span></label>
                            <input wire:model="city" type="text" placeholder="Bali" class="field">
                            @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label">Star rating <span class="text-accent">*</span></label>
                            <select wire:model="star_rating" class="field">
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} star</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Address <span class="text-accent">*</span></label>
                        <input wire:model="address" type="text" placeholder="Street, city" class="field">
                        @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">Description <span class="text-accent">*</span></label>
                        <textarea wire:model="description" rows="3" placeholder="What makes this hotel worth staying at" class="field"></textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label">Photo</label>
                            <input type="file" wire:model="img" accept="image/*" class="field py-2">
                            @error('img') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            <div wire:loading wire:target="img" class="mt-1 text-xs text-muted">Uploading…</div>
                        </div>
                        <div>
                            <label class="label">Status</label>
                            <select wire:model="status" class="field">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    @if ($img || $existingImg)
                        <img src="{{ $img ? $img->temporaryUrl() : asset($existingImg) }}" alt="Preview" class="h-32 w-full rounded-control object-cover">
                    @endif

                    <div class="flex justify-end gap-2 border-t border-line pt-4">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-ghost border border-line">Cancel</button>
                        <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">{{ $editingId ? 'Save changes' : 'Create hotel' }}</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
