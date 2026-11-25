<div>
    <div class="grid gap-6 lg:grid-cols-[260px_1fr]">
        <aside class="hidden lg:block">
            <div class="sticky top-4 space-y-5 rounded-card border border-line bg-white p-4">
                <div>
                    <p class="label">Destination</p>
                    <input wire:model.live.debounce.400ms="city" list="search-cities" type="text" placeholder="City or hotel name" class="field">
                    <datalist id="search-cities">
                        @foreach ($cities as $city)
                            <option value="{{ $city }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="label">Check-in</p>
                        <input wire:model.live="check_in" type="date" min="{{ now()->toDateString() }}" class="field px-2">
                    </div>
                    <div>
                        <p class="label">Check-out</p>
                        <input wire:model.live="check_out" type="date" min="{{ now()->addDay()->toDateString() }}" class="field px-2">
                    </div>
                </div>

                <div>
                    <p class="label">Guests</p>
                    <select wire:model.live="guests" class="field">
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'guest' : 'guests' }}</option>
                        @endfor
                    </select>
                </div>

                <div class="border-t border-line pt-4">
                    <p class="label">Price per night</p>
                    <div class="grid grid-cols-2 gap-2">
                        <input wire:model.live.debounce.500ms="min_price" type="number" min="0" step="50000" placeholder="Min" class="field px-2">
                        <input wire:model.live.debounce.500ms="max_price" type="number" min="0" step="50000" placeholder="Max" class="field px-2">
                    </div>
                </div>

                <div class="border-t border-line pt-4">
                    <p class="label">Star rating</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ([5, 4, 3] as $s)
                            <button type="button" wire:click="$set('star', {{ $star === $s ? 'null' : $s }})"
                                    class="rounded-control border px-2.5 py-1.5 text-xs font-semibold {{ $star === $s ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-line text-muted hover:border-brand-300' }}">
                                {{ $s }}★
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-line pt-4">
                    <p class="label">Facilities</p>
                    <div class="space-y-1.5">
                        @foreach ($facilities as $item)
                            <label class="flex cursor-pointer items-center gap-2 text-sm">
                                <input type="radio" name="facility" wire:model.live="facility" value="{{ $item->id }}"
                                       class="border-line text-brand-500 focus:ring-brand-500">
                                <span class="text-muted">{{ $item->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="button" wire:click="clearFilters" class="w-full rounded-control border border-line py-2 text-sm font-semibold text-muted hover:bg-surface">
                    Reset filters
                </button>
            </div>
        </aside>

        <div class="min-w-0">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-card border border-line bg-white px-4 py-3">
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold">
                        {{ $hotels->total() }} {{ $hotels->total() === 1 ? 'hotel' : 'hotels' }} available
                    </p>
                    <p class="text-xs text-muted">{{ \Illuminate\Support\Carbon::parse($check_in)->format('j M Y') }} → {{ \Illuminate\Support\Carbon::parse($check_out)->format('j M Y') }} · {{ $nights }} {{ $nights === 1 ? 'night' : 'nights' }} · {{ $guests }} {{ $guests === 1 ? 'guest' : 'guests' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="$dispatch('toggle-filters')" class="btn-ghost border border-line lg:hidden" onclick="document.getElementById('mobile-filters').classList.toggle('hidden')">
                        Filters
                    </button>
                    <select wire:model.live="sort" class="field w-auto py-2 text-sm">
                        <option value="recommended">Recommended</option>
                        <option value="price_asc">Price: low to high</option>
                        <option value="price_desc">Price: high to low</option>
                        <option value="rating">Guest rating</option>
                    </select>
                </div>
            </div>

            <div id="mobile-filters" class="mb-4 hidden rounded-card border border-line bg-white p-4 lg:hidden">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <p class="label">Destination</p>
                        <input wire:model.live.debounce.400ms="city" type="text" placeholder="City or hotel name" class="field">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <p class="label">Check-in</p>
                            <input wire:model.live="check_in" type="date" class="field px-2">
                        </div>
                        <div>
                            <p class="label">Check-out</p>
                            <input wire:model.live="check_out" type="date" class="field px-2">
                        </div>
                    </div>
                </div>
                <button type="button" wire:click="clearFilters" class="mt-3 text-xs font-semibold text-brand-600">Reset filters</button>
            </div>

            @if ($hotels->isEmpty())
                <div class="rounded-card border border-line bg-white p-10 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-surface text-muted"><x-icon name="search" class="h-5 w-5" /></div>
                    <p class="mt-4 text-base font-bold">No hotels match these filters</p>
                    <p class="mt-1 text-sm text-muted">Try different dates, raise the price limit, or clear the filters.</p>
                    <button type="button" wire:click="clearFilters" class="btn-primary mt-5">Clear filters</button>
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" wire:loading.class="opacity-50">
                    @foreach ($hotels as $hotel)
                        <x-front.hotel-card :hotel="$hotel" :check-in="$check_in" :check-out="$check_out" />
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $hotels->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
