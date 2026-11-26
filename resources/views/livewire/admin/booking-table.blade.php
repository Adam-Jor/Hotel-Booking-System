@php
    $badge = fn ($status) => match ($status) {
        'paid' => 'bg-emerald-50 text-emerald-700',
        'pending' => 'bg-amber-50 text-amber-700',
        'completed' => 'bg-brand-50 text-brand-700',
        'cancelled' => 'bg-red-50 text-red-700',
        'draft' => 'bg-surface text-muted',
        default => 'bg-surface text-muted',
    };
@endphp

<div>
    <div class="rounded-card border border-line bg-white">
        <div class="flex flex-wrap items-center gap-2 border-b border-line p-4">
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search code, guest or hotel…" class="field w-full max-w-xs">
            <select wire:model.live="status" class="field w-40">
                <option value="">All status</option>
                @foreach (['draft', 'pending', 'paid', 'completed', 'cancelled'] as $item)
                    <option value="{{ $item }}">{{ ucfirst($item) }}</option>
                @endforeach
            </select>
            <span class="ml-auto text-xs text-muted">{{ $bookings->total() }} bookings</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface">
                    <tr>
                        <th class="table-th">Code</th>
                        <th class="table-th">Guest</th>
                        <th class="table-th">Hotel & room</th>
                        <th class="table-th">Stay</th>
                        <th class="table-th">Total</th>
                        <th class="table-th">Status</th>
                        <th class="table-th text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($bookings as $booking)
                        @php($payment = $booking->payments->sortByDesc('id')->first())
                        <tr class="hover:bg-surface/60">
                            <td class="table-td font-semibold">{{ $booking->booking_code }}</td>
                            <td class="table-td">
                                <p class="font-medium">{{ $booking->customer_name }}</p>
                                <p class="text-xs text-muted">{{ $booking->user?->phone }}</p>
                            </td>
                            <td class="table-td">
                                <p class="font-medium">{{ $booking->room?->hotel?->name }}</p>
                                <p class="text-xs text-muted">{{ $booking->room?->roomType?->name }} · {{ $booking->room?->room_number }}</p>
                            </td>
                            <td class="table-td whitespace-nowrap">
                                {{ $booking->check_in_date->format('j M') }} → {{ $booking->check_out_date->format('j M Y') }}
                                <p class="text-xs text-muted">{{ $booking->total_nights }} nights · {{ $booking->guest_count }} guests</p>
                            </td>
                            <td class="table-td whitespace-nowrap font-semibold">Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</td>
                            <td class="table-td">
                                <span class="badge {{ $badge($booking->status) }}">{{ $booking->status_label }}</span>
                                @if ($payment)
                                    <p class="mt-1 text-[11px] text-muted">{{ $payment->method_label }} · {{ ucfirst($payment->status) }}</p>
                                @endif
                            </td>
                            <td class="table-td">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" wire:click="openDetail({{ $booking->id }})" class="rounded p-1.5 text-muted hover:bg-surface hover:text-brand-600" title="Detail">
                                        <x-icon name="search" class="h-4 w-4" />
                                    </button>
                                    <a href="{{ route('bookings.invoice', $booking) }}" class="rounded p-1.5 text-muted hover:bg-surface hover:text-brand-600" title="Invoice">
                                        <x-icon name="download" class="h-4 w-4" />
                                    </a>
                                    <button type="button" wire:click="resend({{ $booking->id }})" class="rounded p-1.5 text-muted hover:bg-surface hover:text-brand-600" title="Resend WhatsApp">
                                        <x-icon name="message-circle" class="h-4 w-4" />
                                    </button>
                                    @if (in_array($booking->status, ['pending', 'draft']))
                                        <button type="button" wire:click="markPaid({{ $booking->id }})" wire:confirm="Record a cash payment for this booking?" class="rounded p-1.5 text-muted hover:bg-emerald-50 hover:text-emerald-700" title="Mark paid (cash)">
                                            <x-icon name="credit-card" class="h-4 w-4" />
                                        </button>
                                    @endif
                                    @if ($booking->status === 'paid')
                                        <button type="button" wire:click="complete({{ $booking->id }})" wire:confirm="Mark this stay as completed?" class="rounded p-1.5 text-muted hover:bg-brand-50 hover:text-brand-700" title="Complete">
                                            <x-icon name="check" class="h-4 w-4" />
                                        </button>
                                    @endif
                                    @if (in_array($booking->status, ['pending', 'paid', 'draft']))
                                        <button type="button" wire:click="cancel({{ $booking->id }})" wire:confirm="Cancel this booking and release the room?" class="rounded p-1.5 text-muted hover:bg-red-50 hover:text-red-600" title="Cancel">
                                            <x-icon name="x" class="h-4 w-4" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-muted">No bookings match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line p-4">{{ $bookings->links() }}</div>
    </div>

    @if ($detail)
        @php($detailPayment = $detail->payments->sortByDesc('id')->first())
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-ink/40 p-4 sm:items-center">
            <div class="w-full max-w-2xl rounded-card bg-white shadow-float">
                <div class="flex items-center justify-between border-b border-line px-5 py-4">
                    <div>
                        <h2 class="text-base font-bold">{{ $detail->booking_code }}</h2>
                        <p class="text-xs text-muted">Created {{ $detail->created_at->format('j M Y H:i') }}</p>
                    </div>
                    <button type="button" wire:click="$set('detailId', null)" class="rounded p-1 text-muted hover:bg-surface"><x-icon name="x" class="h-5 w-5" /></button>
                </div>

                <div class="space-y-4 p-5">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-control bg-surface p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-muted">Guest</p>
                            <p class="mt-1 text-sm font-semibold">{{ $detail->customer_name }}</p>
                            <p class="text-xs text-muted">{{ $detail->user?->email }} · {{ $detail->user?->phone }}</p>
                        </div>
                        <div class="rounded-control bg-surface p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-muted">Hotel</p>
                            <p class="mt-1 text-sm font-semibold">{{ $detail->room?->hotel?->name }}</p>
                            <p class="text-xs text-muted">{{ $detail->room?->roomType?->name }} · Room {{ $detail->room?->room_number }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div><p class="text-[11px] text-muted">Check-in</p><p class="text-sm font-semibold">{{ $detail->check_in_date->format('j M Y') }}</p></div>
                        <div><p class="text-[11px] text-muted">Check-out</p><p class="text-sm font-semibold">{{ $detail->check_out_date->format('j M Y') }}</p></div>
                        <div><p class="text-[11px] text-muted">Total</p><p class="text-sm font-bold text-accent">Rp {{ number_format((float) $detail->total_price, 0, ',', '.') }}</p></div>
                    </div>

                    @if ($detail->notes)
                        <div class="rounded-control border border-line p-3 text-sm text-muted">{{ $detail->notes }}</div>
                    @endif

                    <div>
                        <p class="text-sm font-bold">Payments</p>
                        <div class="mt-2 space-y-2">
                            @forelse ($detail->payments as $payment)
                                <div class="flex flex-wrap items-center justify-between gap-2 rounded-control border border-line px-3 py-2 text-sm">
                                    <div>
                                        <p class="font-semibold">{{ $payment->method_label }} · Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                                        <p class="text-xs text-muted">{{ $payment->reference_code }} · {{ $payment->created_at->format('j M Y H:i') }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if ($payment->proof_url)
                                            <a href="{{ asset('storage/'.$payment->proof_url) }}" target="_blank" class="text-xs font-semibold text-brand-600">Proof</a>
                                        @endif
                                        <span class="badge {{ $badge($payment->status) }}">{{ ucfirst($payment->status) }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-muted">No payment recorded.</p>
                            @endforelse
                        </div>
                    </div>

                    @if ($detailPayment && $detailPayment->reject_reason)
                        <p class="rounded-control bg-red-50 px-3 py-2 text-xs font-medium text-red-700">Rejected: {{ $detailPayment->reject_reason }}</p>
                    @endif
                </div>

                <div class="flex flex-wrap justify-end gap-2 border-t border-line px-5 py-4">
                    <a href="{{ route('bookings.invoice', $detail) }}" class="btn-ghost border border-line"><x-icon name="download" class="h-4 w-4" /> Invoice</a>
                    <button type="button" wire:click="$set('detailId', null)" class="btn-primary">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
