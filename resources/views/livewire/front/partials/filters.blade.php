@php
    $activeFilters = 0;
    $activeFilters += $city ? 1 : 0;
    $activeFilters += $min_price ? 1 : 0;
    $activeFilters += $max_price ? 1 : 0;
    $activeFilters += $star ? 1 : 0;
    $activeFilters += $facility ? 1 : 0;
@endphp

<div>
    <label class="label">Destination</label>
    <x-city-autocomplete wire-model="city" :cities="$cities" live debounce="400" placeholder="City or hotel name" />
</div>

<div class="grid grid-cols-2 gap-2">
    <div>
        <label class="label">Check-in</label>
        <input wire:model.live="check_in" type="date" min="{{ now()->toDateString() }}" class="field px-2">
    </div>
    <div>
        <label class="label">Check-out</label>
        <input wire:model.live="check_out" type="date" min="{{ now()->addDay()->toDateString() }}" class="field px-2">
    </div>
</div>

<div>
    <label class="label">Guests</label>
    <select wire:model.live="guests" class="field">
        @for ($i = 1; $i <= 6; $i++)
            <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'guest' : 'guests' }}</option>
        @endfor
    </select>
</div>

<div class="border-t border-line pt-4">
    <label class="label">Price per night</label>
    <div class="grid grid-cols-2 gap-2">
        <input wire:model.live.debounce.500ms="min_price" type="number" min="0" step="50000" placeholder="Min (IDR)" class="field px-2">
        <input wire:model.live.debounce.500ms="max_price" type="number" min="0" step="50000" placeholder="Max (IDR)" class="field px-2">
    </div>
</div>

<div class="border-t border-line pt-4">
    <label class="label">Star rating</label>
    <div class="flex flex-wrap gap-1.5">
        @foreach ([5, 4, 3] as $s)
            <button type="button" wire:click="$set('star', {{ $star === $s ? 'null' : $s }})"
                    class="rounded-control border px-3 py-1.5 text-xs font-semibold
                           {{ $star === $s ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-line text-muted hover:border-brand-300' }}">
                {{ $s }}★
            </button>
        @endforeach
    </div>
</div>

<div class="border-t border-line pt-4">
    <label class="label">Facilities</label>
    <div class="space-y-1.5 max-h-40 overflow-y-auto">
        @foreach ($facilities as $item)
            <label class="flex cursor-pointer items-center gap-2 text-sm">
                <input type="radio" name="facility" wire:model.live="facility" value="{{ $item->id }}" class="h-4 w-4 border-line text-brand-500 focus:ring-brand-500">
                <span class="text-muted">{{ $item->name }}</span>
            </label>
        @endforeach
    </div>
</div>