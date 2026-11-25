@php
    $badge = fn ($status) => match ($status) {
        'paid' => 'bg-emerald-50 text-emerald-700',
        'pending' => 'bg-amber-50 text-amber-700',
        'completed' => 'bg-brand-50 text-brand-700',
        'cancelled' => 'bg-red-50 text-red-700',
        default => 'bg-surface text-muted',
    };
@endphp

<div>
    <div class="no-scrollbar mb-5 flex gap-2 overflow-x-auto">
        @foreach (['all' => 'All', 'pending' => 'Pending', 'paid' => 'Paid', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label)
            <button type="button" wire:click="setStatus('{{ $key }}')"
                    class="shrink-0 rounded-full border px-4 py-1.5 text-sm font-semibold {{ $status === $key ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-line bg-white text-muted hover:border-brand-300' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    @if ($bookings->isEmpty())
        <div class="rounded-card border border-line bg-white p-10 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-surface text-muted"><x-icon name="calendar" class="h-5 w-5" /></div>
            <p class="mt-4 text-base font-bold">No bookings here yet</p>
            <p class="mt-1 text-sm text-muted">When you book a room it will show up on this page.</p>
            <a href="{{ route('hotels.index') }}" wire:navigate class="btn-primary mt-5">Find a hotel</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($bookings as $booking)
                @php($payment = $booking->payments->sortByDesc('id')->first())
                @php($needsVerification = $payment && $payment->status === 'pending' && $payment->proof_url)
                <div class="rounded-card border border-line bg-white p-4">
                    <div class="flex flex-col gap-4 sm:flex-row">
                        <img src="{{ $booking->room->image }}" alt="{{ $booking->room->hotel->name }}" loading="lazy" width="220" height="150"
                             class="h-32 w-full rounded-control object-cover sm:w-44">

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-muted">{{ $booking->booking_code }}</p>
                                    <h2 class="mt-0.5 text-base font-bold">{{ $booking->room->hotel->name }}</h2>
                                    <p class="text-sm text-muted">{{ $booking->room->hotel->city }} · {{ $booking->room->roomType->name }} · Room {{ $booking->room->room_number }}</p>
                                </div>
                                <span class="badge {{ $badge($booking->status) }}">{{ $booking->status_label }}</span>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-1 text-sm">
                                <span class="inline-flex items-center gap-1.5 text-muted"><x-icon name="calendar" class="h-4 w-4" /> {{ $booking->check_in_date->format('j M Y') }} → {{ $booking->check_out_date->format('j M Y') }}</span>
                                <span class="text-muted">{{ $booking->total_nights }} {{ $booking->total_nights === 1 ? 'night' : 'nights' }} · {{ $booking->guest_count }} guests</span>
                                <span class="font-bold text-accent">Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</span>
                            </div>

                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                @if ($payment)
                                    <span class="badge {{ $badge($payment->status) }}">{{ $payment->method_label }} · {{ ucfirst($payment->status) }}</span>
                                @endif
                                @if ($needsVerification)
                                    <span class="badge bg-amber-50 text-amber-700">Needs verification</span>
                                @endif
                                @if ($booking->isPending() && $booking->expires_at)
                                    <span class="inline-flex items-center gap-1 font-medium text-amber-700"
                                          x-data="{ left: Math.max(0, {{ $booking->expires_at->timestamp }} - Math.floor(Date.now()/1000)) }"
                                          x-init="setInterval(() => { if (left > 0) left-- }, 1000)">
                                        <x-icon name="clock" class="h-3.5 w-3.5" />
                                        Pay within <span x-text="String(Math.floor(left/60)).padStart(2,'0') + ':' + String(left%60).padStart(2,'0')"></span>
                                    </span>
                                @endif
                            </div>

                            @if ($booking->notes)
                                <p class="mt-2 rounded-control bg-surface px-3 py-2 text-xs text-muted">{{ $booking->notes }}</p>
                            @endif

                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                @if ($booking->status === 'pending')
                                    @if ($payment && $payment->payment_method === 'ewallet_sim')
                                        <button type="button" wire:click="payNow({{ $booking->id }})" class="btn-primary px-3 py-2 text-xs">Pay now</button>
                                    @elseif (! $needsVerification)
                                        <button type="button" wire:click="openProof({{ $booking->id }})" class="btn-primary px-3 py-2 text-xs">Upload proof</button>
                                    @endif
                                    <button type="button" wire:click="confirmCancel({{ $booking->id }})" class="btn-ghost border border-line px-3 py-2 text-xs text-red-600 hover:bg-red-50">Cancel booking</button>
                                @endif

                                @if (in_array($booking->status, ['paid', 'completed']))
                                    <a href="{{ route('bookings.invoice', $booking) }}" class="btn-ghost border border-line px-3 py-2 text-xs">
                                        <x-icon name="download" class="h-3.5 w-3.5" /> Invoice
                                    </a>
                                @endif

                                @if ($booking->status === 'completed' && ! $booking->review)
                                    <button type="button" wire:click="openReview({{ $booking->id }})" class="btn-ghost border border-line px-3 py-2 text-xs">Write a review</button>
                                @endif

                                <a href="{{ route('hotels.show', $booking->room->hotel->slug) }}" wire:navigate class="btn-ghost px-3 py-2 text-xs text-brand-600">View hotel</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($cancelId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40 p-4">
            <div class="w-full max-w-sm rounded-card bg-white p-5 shadow-float">
                <h3 class="text-base font-bold">Cancel this booking?</h3>
                <p class="mt-1 text-sm text-muted">The room will be released and this cannot be undone. Only pending bookings can be cancelled here.</p>
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" wire:click="$set('cancelId', null)" class="btn-ghost border border-line">Keep booking</button>
                    <button type="button" wire:click="cancel" class="btn-primary bg-red-600 hover:bg-red-700">Cancel booking</button>
                </div>
            </div>
        </div>
    @endif

    @if ($proofId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40 p-4">
            <div class="w-full max-w-sm rounded-card bg-white p-5 shadow-float">
                <h3 class="text-base font-bold">Upload payment proof</h3>
                <p class="mt-1 text-sm text-muted">JPG, PNG or PDF up to 2 MB.</p>
                <input type="file" wire:model="proof" accept=".jpg,.jpeg,.png,.pdf" class="field mt-3 py-2">
                @error('proof') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <div wire:loading wire:target="proof" class="mt-2 text-xs text-muted">Uploading…</div>
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" wire:click="$set('proofId', null)" class="btn-ghost border border-line">Close</button>
                    <button type="button" wire:click="uploadProof" class="btn-primary">Upload</button>
                </div>
            </div>
        </div>
    @endif

    @if ($reviewId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40 p-4">
            <div class="w-full max-w-md rounded-card bg-white p-5 shadow-float">
                <h3 class="text-base font-bold">Write a review</h3>
                <p class="mt-1 text-sm text-muted">Only guests with a completed stay can review.</p>
                <div class="mt-3 flex gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <button type="button" wire:click="$set('rating', {{ $i }})" class="p-0.5">
                            <x-icon name="star" class="h-6 w-6 {{ $i <= $rating ? 'fill-accent text-accent' : 'text-line' }}" />
                        </button>
                    @endfor
                </div>
                <textarea wire:model="comment" rows="3" placeholder="How was your stay?" class="field mt-3"></textarea>
                @error('comment') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" wire:click="$set('reviewId', null)" class="btn-ghost border border-line">Close</button>
                    <button type="button" wire:click="submitReview" class="btn-primary">Submit review</button>
                </div>
            </div>
        </div>
    @endif
</div>
