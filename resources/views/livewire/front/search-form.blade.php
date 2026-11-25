<div>
    <form wire:submit="search" class="rounded-card bg-white p-3 shadow-float sm:p-4">
        <div class="flex flex-wrap items-end gap-2">
            <div class="min-w-[180px] flex-1">
                <label class="mb-1 block text-xs font-semibold text-muted">City, hotel, or destination</label>
                <div class="flex items-center gap-2 rounded-control border border-line px-3 focus-within:border-brand-500">
                    <x-icon name="map-pin" class="h-4 w-4 shrink-0 text-brand-500" />
                    <input wire:model="city" list="city-options" type="text" placeholder="Bali, Jakarta, Yogyakarta…"
                           class="h-11 w-full border-0 bg-transparent p-0 text-sm font-medium text-ink placeholder:text-muted/70 focus:ring-0">
                </div>
                <datalist id="city-options">
                    @foreach ($cities as $city)
                        <option value="{{ $city }}"></option>
                    @endforeach
                </datalist>
            </div>

            <div class="min-w-[150px] flex-1">
                <label class="mb-1 block text-xs font-semibold text-muted">Check-in</label>
                <div class="flex items-center gap-2 rounded-control border border-line px-3 focus-within:border-brand-500">
                    <x-icon name="calendar" class="h-4 w-4 shrink-0 text-brand-500" />
                    <input wire:model="check_in" type="date" min="{{ now()->toDateString() }}"
                           class="h-11 w-full border-0 bg-transparent p-0 text-sm font-medium text-ink focus:ring-0">
                </div>
            </div>

            <div class="min-w-[150px] flex-1">
                <label class="mb-1 block text-xs font-semibold text-muted">Check-out</label>
                <div class="flex items-center gap-2 rounded-control border border-line px-3 focus-within:border-brand-500">
                    <x-icon name="calendar" class="h-4 w-4 shrink-0 text-brand-500" />
                    <input wire:model="check_out" type="date" min="{{ now()->addDay()->toDateString() }}"
                           class="h-11 w-full border-0 bg-transparent p-0 text-sm font-medium text-ink focus:ring-0">
                </div>
            </div>

            <div class="min-w-[130px] flex-1">
                <label class="mb-1 block text-xs font-semibold text-muted">Guests</label>
                <div class="flex items-center gap-2 rounded-control border border-line px-3 focus-within:border-brand-500">
                    <x-icon name="users" class="h-4 w-4 shrink-0 text-brand-500" />
                    <select wire:model="guests" class="h-11 w-full border-0 bg-transparent p-0 text-sm font-medium text-ink focus:ring-0">
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'guest' : 'guests' }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-primary h-11 min-w-[120px] gap-2" wire:loading.attr="disabled">
                <x-icon name="search" class="h-4 w-4" />
                <span wire:loading.remove wire:target="search">Search</span>
                <span wire:loading wire:target="search">…</span>
            </button>
        </div>
        @error('check_in') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
        @error('check_out') <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
    </form>
</div>
