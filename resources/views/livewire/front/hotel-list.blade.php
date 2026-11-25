<div x-data="{ filtersOpen: false }" class="w-full">
    {{-- Toolbar --}}
    <div class="mb-4 flex items-center justify-between gap-2 rounded-card border border-line bg-white px-3 py-2.5 sm:px-4">
        <div class="min-w-0">
            <p class="truncate text-sm font-bold">
                {{ $hotels->total() }} {{ $hotels->total() === 1 ? 'hotel' : 'hotels' }}
            </p>
            <p class="truncate text-xs text-muted">{{ \Illuminate\Support\Carbon::parse($check_in)->format('j M') }} – {{ \Illuminate\Support\Carbon::parse($check_out)->format('j M') }} · {{ $nights }} {{ $nights === 1 ? 'night' : 'nights' }} · {{ $guests }} {{ $guests === 1 ? 'guest' : 'guests' }}</p>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            <button type="button" @click="filtersOpen = true"
                    class="inline-flex items-center gap-1.5 rounded-control border border-line bg-white px-3 py-2 text-sm font-semibold hover:border-brand-300 lg:hidden">
                <x-icon name="sliders-horizontal" class="h-4 w-4" />
                <span>Filter</span>
            </button>

            <div class="relative">
                <select wire:model.live="sort"
                        class="h-10 appearance-none rounded-control border border-line bg-white pl-3 pr-8 text-sm font-semibold text-ink focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    <option value="recommended">Recommended</option>
                    <option value="price_asc">Price: low to high</option>
                    <option value="price_desc">Price: high to low</option>
                    <option value="rating">Guest rating</option>
                </select>
                <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" />
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[260px_1fr]">
        {{-- Desktop filters --}}
        <aside class="hidden lg:block">
            <div class="sticky top-20 space-y-5 rounded-card border border-line bg-white p-4">
                @include('livewire.front.partials.filters', ['uid' => 'desktop'])
            </div>
        </aside>

        {{-- Mobile filter drawer --}}
        <div x-show="filtersOpen" x-cloak class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" aria-label="Filters">
            <div @click="filtersOpen = false" class="absolute inset-0 bg-black/50"></div>
            <div x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="translate-y-0"
                 x-transition:leave-end="translate-y-full"
                 class="absolute inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto rounded-t-2xl bg-white">
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-line bg-white px-4 py-3">
                    <h3 class="text-base font-bold">Filters</h3>
                    <button type="button" @click="filtersOpen = false" class="rounded p-1.5 hover:bg-surface" aria-label="Close filters">
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>
                <div class="space-y-5 p-4">
                    @include('livewire.front.partials.filters', ['uid' => 'mobile'])
                </div>
                <div class="sticky bottom-0 flex gap-2 border-t border-line bg-white p-4">
                    <button type="button" wire:click="clearFilters" @click="filtersOpen = false" class="btn-secondary flex-1">Reset</button>
                    <button type="button" @click="filtersOpen = false" class="btn-primary flex-1">Show {{ $hotels->total() }}</button>
                </div>
            </div>
        </div>

        {{-- Results --}}
        <div class="min-w-0">
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
                <div class="mt-6">{{ $hotels->links() }}</div>
            @endif
        </div>
    </div>
</div>
