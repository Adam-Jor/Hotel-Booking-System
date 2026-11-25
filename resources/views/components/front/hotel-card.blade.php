@props(['hotel', 'checkIn' => null, 'checkOut' => null])

@php
    $price = $hotel->rooms_min_price ?? $hotel->rooms->min('price');
    $rating = round((float) ($hotel->reviews_avg_rating ?? 0), 1);
@endphp

<a href="{{ route('hotels.show', $hotel->slug) }}" wire:navigate
   class="group overflow-hidden rounded-card border border-line bg-white transition-shadow hover:shadow-card">
    <div class="relative aspect-[16/10] overflow-hidden bg-surface">
        <img src="{{ $hotel->image }}" alt="{{ $hotel->name }}" loading="lazy" width="640" height="400"
             class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]">
        @if ($hotel->star_rating)
            <span class="absolute left-3 top-3 rounded bg-white/95 px-2 py-1 text-[11px] font-bold text-ink shadow-sm">
                {{ $hotel->star_rating }}-star hotel
            </span>
        @endif
    </div>
    <div class="p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-xs font-medium text-muted">{{ $hotel->city }}</p>
                <h3 class="mt-0.5 truncate text-[15px] font-bold text-ink">{{ $hotel->name }}</h3>
            </div>
            @if ($rating > 0)
                <span class="flex shrink-0 items-center gap-1 rounded-control bg-brand-50 px-2 py-1 text-xs font-bold text-brand-700">
                    <x-icon name="star" class="h-3.5 w-3.5 fill-brand-500 text-brand-500" /> {{ number_format($rating, 1) }}
                </span>
            @endif
        </div>
        <div class="mt-2 flex flex-wrap items-center gap-1.5">
            @foreach ($hotel->facilities->take(3) as $facility)
                <span class="rounded-full bg-surface px-2 py-0.5 text-[11px] font-medium text-muted">{{ $facility->name }}</span>
            @endforeach
        </div>
        <div class="mt-4 flex items-end justify-between border-t border-line pt-3">
            <div>
                <p class="text-[11px] text-muted">Starting from</p>
                <p class="text-base font-extrabold text-accent">
                    Rp {{ number_format((float) $price, 0, ',', '.') }}
                    <span class="text-[11px] font-medium text-muted">/night</span>
                </p>
            </div>
            <span class="text-xs font-semibold text-brand-600 group-hover:text-brand-700">View rooms</span>
        </div>
    </div>
</a>
