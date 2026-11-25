@props([
    'wireModel' => 'city',
    'cities' => [],
    'placeholder' => 'City or hotel name',
    'live' => false,
    'debounce' => null,
    'icon' => 'map-pin',
])

@php
    $model = 'wire:model';
    if ($live) {
        $model .= '.live';
    }
    if ($debounce) {
        $model .= '.debounce.'.$debounce.'ms';
    }
@endphp

<div x-data="{
        open: false,
        query: '',
        cities: @js(collect($cities)->values()),
        get filtered() {
            if (!this.query) return this.cities;
            const q = this.query.toLowerCase();
            return this.cities.filter(c => c.toLowerCase().includes(q));
        },
        choose(value) {
            this.$refs.input.value = value;
            this.$refs.input.dispatchEvent(new Event('input', { bubbles: true }));
            this.query = value;
            this.open = false;
        }
     }"
     @click.outside="open = false"
     class="relative">
    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-brand-500">
        <x-icon :name="$icon" class="h-5 w-5" />
    </span>
    <input x-ref="input"
           {{ $model }}="{{ $wireModel }}"
           type="text"
           placeholder="{{ $placeholder }}"
           autocomplete="off"
           class="field pl-10 pr-10"
           @focus="open = true; query = $event.target.value"
           @input="open = true; query = $event.target.value">
    <button type="button" x-show="open" x-cloak @click="open = false"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-muted hover:text-ink"
            aria-label="Close suggestions">
        <x-icon name="x" class="h-4 w-4" />
    </button>

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="absolute z-50 mt-1.5 max-h-64 w-full overflow-auto rounded-card border border-line bg-white py-1 shadow-float">
        <template x-for="city in filtered" :key="city">
            <button type="button" @click="choose(city)"
                    class="flex w-full items-center gap-2 px-3.5 py-2.5 text-left text-sm font-medium text-ink transition-colors hover:bg-surface">
                <x-icon name="map-pin" class="h-4 w-4 shrink-0 text-muted" />
                <span x-text="city"></span>
            </button>
        </template>
        <template x-if="filtered.length === 0">
            <p class="px-3.5 py-2.5 text-sm text-muted">No destinations found</p>
        </template>
    </div>
</div>
