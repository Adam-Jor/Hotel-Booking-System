<div class="space-y-5">
    <div class="rounded-card border border-line bg-white p-4">
        <div class="grid gap-3 sm:grid-cols-[1fr_1fr_auto_auto] sm:items-end">
            <div>
                <p class="label">Check-in</p>
                <input wire:model.live="check_in" type="date" min="{{ now()->toDateString() }}" class="field">
            </div>
            <div>
                <p class="label">Check-out</p>
                <input wire:model.live="check_out" type="date" min="{{ now()->addDay()->toDateString() }}" class="field">
            </div>
            <div>
                <p class="label">Guests</p>
                <select wire:model.live="guests" class="field">
                    @for ($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="rounded-control bg-surface px-3 py-2.5 text-sm">
                <span class="font-bold">{{ $nights }}</span> <span class="text-muted">{{ $nights === 1 ? 'night' : 'nights' }}</span>
            </div>
        </div>
        <p class="mt-2 text-xs text-muted" wire:loading>Checking availability…</p>
    </div>

    @if ($rooms->isEmpty())
        <div class="rounded-card border border-line bg-white p-8 text-center">
            <p class="text-base font-bold">No rooms left for these dates</p>
            <p class="mt-1 text-sm text-muted">Try shifting your dates by a day or two.</p>
        </div>
    @else
        <div class="space-y-3" wire:loading.class="opacity-60">
            @foreach ($rooms as $room)
                @php($total = (float) $room->price * $nights)
                @php($fits = $guests <= $room->roomType->capacity)
                <div class="flex flex-col gap-4 rounded-card border border-line bg-white p-4 sm:flex-row sm:items-center">
                    <img src="{{ $room->image }}" alt="{{ $room->roomType->name }}" loading="lazy" width="200" height="140"
                         class="h-28 w-full rounded-control object-cover sm:w-40">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-base font-bold">{{ $room->roomType->name }}</h3>
                            <span class="rounded-full bg-surface px-2 py-0.5 text-[11px] font-semibold text-muted">Room {{ $room->room_number }}</span>
                        </div>
                        <p class="mt-1 line-clamp-2 text-sm text-muted">{{ $room->description ?? $room->roomType->description }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-muted">
                            <span class="inline-flex items-center gap-1"><x-icon name="users" class="h-3.5 w-3.5" /> Up to {{ $room->roomType->capacity }} guests</span>
                            <span class="inline-flex items-center gap-1"><x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" /> Free cancellation while pending</span>
                        </div>
                        @unless ($fits)
                            <p class="mt-2 text-xs font-semibold text-red-600">Room capacity is {{ $room->roomType->capacity }} guests.</p>
                        @endunless
                    </div>
                    <div class="shrink-0 text-left sm:text-right">
                        <p class="text-lg font-extrabold text-accent">Rp {{ number_format((float) $room->price, 0, ',', '.') }}</p>
                        <p class="text-xs text-muted">per night · Rp {{ number_format($total, 0, ',', '.') }} total</p>
                        @if ($fits)
                            <a href="{{ route('checkout', ['room_id' => $room->id, 'check_in' => $check_in, 'check_out' => $check_out, 'guests' => $guests]) }}"
                               wire:navigate class="btn-primary mt-2 w-full sm:w-auto">Select room</a>
                        @else
                            <button type="button" disabled class="btn-primary mt-2 w-full opacity-50 sm:w-auto">Too many guests</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
