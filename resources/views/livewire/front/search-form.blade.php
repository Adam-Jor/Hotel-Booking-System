<div x-data="{ guestsOpen: false }" class="w-full">
    <form wire:submit="search" class="rounded-card bg-white p-3 shadow-float sm:p-4 space-y-3 sm:space-y-0 sm:grid sm:grid-cols-[1fr_1fr_1fr_1fr_auto] sm:items-end sm:gap-3">
        <div class="min-w-0">
            <label class="label">City, hotel, or destination</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <x-icon name="map-pin" class="h-5 w-5 text-brand-500" />
                </div>
                <input wire:model.debounce.400ms="city"
                       list="city-options"
                       type="text"
                       placeholder="Bali, Jakarta, Yogyakarta…"
                       class="field pl-10 pr-3 py-2.5"
                       autocomplete="off">
                <datalist id="city-options">
                    @foreach ($cities as $city)
                        <option value="{{ $city }}"></option>
                    @endforeach
                </datalist>
            </div>
        </div>

        <div class="min-w-0">
            <label class="label">Check-in</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <x-icon name="calendar" class="h-5 w-5 text-brand-500" />
                </div>
                <input wire:model="check_in"
                       type="date"
                       min="{{ now()->toDateString() }}"
                       class="field pl-10 pr-3 py-2.5">
            </div>
            @error('check_in') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="min-w-0">
            <label class="label">Check-out</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <x-icon name="calendar" class="h-5 w-5 text-brand-500" />
                </div>
                <input wire:model="check_out"
                       type="date"
                       min="{{ now()->addDay()->toDateString() }}"
                       class="field pl-10 pr-3 py-2.5">
            </div>
            @error('check_out') <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="min-w-0 relative">
            <label class="label">Guests <span class="text-normal font-normal text-xs text-muted">(max 6)</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <x-icon name="users" class="h-5 w-5 text-brand-500" />
                </div>
                <button type="button"
                        @click="guestsOpen = !guestsOpen"
                        @keydown.escape="guestsOpen = false"
                        wire:loading.attr="disabled"
                        class="field pl-10 pr-10 py-2.5 text-left"
                        aria-haspopup="listbox"
                        aria-expanded="false">
                    <span>{{ $guests }} {{ $guests === 1 ? 'guest' : 'guests' }}</span>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-muted transition-transform duration-200" :class="{ 'rotate-180': guestsOpen }">
                        <x-icon name="chevron-down" class="h-4 w-4" />
                    </span>
                </button>

                <div x-show="guestsOpen"
                     x-cloak
                     @click.outside="guestsOpen = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 transform -translate-y-1"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-1"
                     class="absolute z-50 mt-1 w-full max-w-xs rounded-card border border-line bg-white shadow-float py-1">
                    @for ($i = 1; $i <= 6; $i++)
                        <button type="button"
                                wire:click="$set('guests', {{ $i }})"
                                @click="guestsOpen = false"
                                class="flex items-center justify-between w-full px-4 py-2.5 text-sm text-left hover:bg-surface transition-colors
                                       {{ $guests === $i ? 'text-brand-600 font-semibold' : 'text-ink' }}">
                            <span>{{ $i }} {{ $i === 1 ? 'guest' : 'guests' }}</span>
                            @if ($guests === $i)
                                <x-icon name="check" class="h-4 w-4 text-brand-500" />
                            @endif
                        </button>
                    @endfor
                </div>
            </div>
        </div>

        <button type="submit"
                class="btn-primary h-12 min-w-[140px] gap-2 justify-center self-end sm:self-auto"
                wire:loading.attr="disabled">
            <x-icon name="search" class="h-5 w-5" />
            <span wire:loading.remove wire:target="search">Search</span>
            <span wire:loading wire:target="search"><span class="loading loading-spinner loading-sm"></span></span>
        </button>
    </form>
</div>