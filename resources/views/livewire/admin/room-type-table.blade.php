<div>
    <div class="rounded-card border border-line bg-white">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line p-4">
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search room type…" class="field w-full max-w-xs">
            <button type="button" wire:click="create" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add room type</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface">
                    <tr>
                        <th class="table-th">Name</th>
                        <th class="table-th">Capacity</th>
                        <th class="table-th">Rooms</th>
                        <th class="table-th">Description</th>
                        <th class="table-th text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($types as $type)
                        <tr class="hover:bg-surface/60">
                            <td class="table-td font-semibold">{{ $type->name }}</td>
                            <td class="table-td">{{ $type->capacity }} guests</td>
                            <td class="table-td">{{ $type->rooms_count }}</td>
                            <td class="table-td max-w-sm truncate text-muted">{{ $type->description }}</td>
                            <td class="table-td">
                                <div class="flex justify-end gap-1">
                                    <button type="button" wire:click="edit({{ $type->id }})" class="rounded p-1.5 text-muted hover:bg-surface hover:text-brand-600"><x-icon name="pencil" class="h-4 w-4" /></button>
                                    <button type="button" wire:click="delete({{ $type->id }})" wire:confirm="Archive this room type?" class="rounded p-1.5 text-muted hover:bg-red-50 hover:text-red-600"><x-icon name="trash" class="h-4 w-4" /></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-muted">No room types yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line p-4">{{ $types->links() }}</div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40 p-4">
            <div class="w-full max-w-md rounded-card bg-white shadow-float">
                <div class="flex items-center justify-between border-b border-line px-5 py-4">
                    <h2 class="text-base font-bold">{{ $editingId ? 'Edit room type' : 'Add room type' }}</h2>
                    <button type="button" wire:click="$set('showModal', false)" class="rounded p-1 text-muted hover:bg-surface"><x-icon name="x" class="h-5 w-5" /></button>
                </div>
                <form wire:submit="save" class="space-y-4 p-5">
                    <div>
                        <label class="label">Name <span class="text-accent">*</span></label>
                        <input wire:model="name" type="text" placeholder="Deluxe" class="field" autofocus>
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">Capacity <span class="text-accent">*</span></label>
                        <input wire:model="capacity" type="number" min="1" max="10" class="field">
                        @error('capacity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">Description</label>
                        <textarea wire:model="description" rows="3" placeholder="Room highlights" class="field"></textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-2 border-t border-line pt-4">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-ghost border border-line">Cancel</button>
                        <button type="submit" class="btn-primary">{{ $editingId ? 'Save changes' : 'Create' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
