@php
    $badge = fn ($status) => match ($status) {
        'paid' => 'bg-emerald-50 text-emerald-700',
        'pending' => 'bg-amber-50 text-amber-700',
        'failed' => 'bg-red-50 text-red-700',
        default => 'bg-surface text-muted',
    };
@endphp

<div>
    <div class="rounded-card border border-line bg-white">
        <div class="flex flex-wrap items-center gap-2 border-b border-line p-4">
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search reference, code or guest…" class="field w-full max-w-xs">
            <select wire:model.live="status" class="field w-36">
                <option value="">All status</option>
                @foreach (['pending', 'paid', 'failed'] as $item)
                    <option value="{{ $item }}">{{ ucfirst($item) }}</option>
                @endforeach
            </select>
            <select wire:model.live="method" class="field w-40">
                <option value="">All methods</option>
                <option value="bank_transfer">Bank transfer</option>
                <option value="ewallet_sim">E-wallet</option>
                <option value="cash">Cash</option>
            </select>
            <span class="ml-auto text-xs text-muted">{{ $payments->total() }} payments</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface">
                    <tr>
                        <th class="table-th">Reference</th>
                        <th class="table-th">Booking</th>
                        <th class="table-th">Method</th>
                        <th class="table-th">Amount</th>
                        <th class="table-th">Proof</th>
                        <th class="table-th">Status</th>
                        <th class="table-th text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-surface/60">
                            <td class="table-td">
                                <p class="font-semibold">{{ $payment->reference_code }}</p>
                                <p class="text-xs text-muted">{{ $payment->created_at->format('j M Y H:i') }}</p>
                            </td>
                            <td class="table-td">
                                <p class="font-medium">{{ $payment->booking?->booking_code }}</p>
                                <p class="text-xs text-muted">{{ $payment->booking?->customer_name }} · {{ $payment->booking?->room?->hotel?->name }}</p>
                            </td>
                            <td class="table-td">{{ $payment->method_label }}</td>
                            <td class="table-td whitespace-nowrap font-semibold">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                            <td class="table-td">
                                @if ($payment->proof_url)
                                    <a href="{{ asset($payment->proof_url) }}" target="_blank" rel="noopener"
                                       class="inline-flex items-center gap-2 text-xs font-semibold text-brand-600 hover:text-brand-700">
                                        @if (str_ends_with(strtolower($payment->proof_url), '.pdf'))
                                            <x-icon name="download" class="h-3.5 w-3.5" /> PDF
                                        @else
                                            <img src="{{ asset($payment->proof_url) }}" alt="Transfer proof"
                                                 loading="lazy" class="h-10 w-10 rounded border border-line object-cover">
                                            <span>Open</span>
                                        @endif
                                    </a>
                                @else
                                    <span class="text-xs text-muted">None</span>
                                @endif
                            </td>
                            <td class="table-td">
                                <span class="badge {{ $badge($payment->status) }}">{{ ucfirst($payment->status) }}</span>
                                @if ($payment->verifier)
                                    <p class="mt-1 text-[11px] text-muted">by {{ $payment->verifier->name }}</p>
                                @endif
                                @if ($payment->reject_reason)
                                    <p class="mt-1 text-[11px] text-red-600">{{ $payment->reject_reason }}</p>
                                @endif
                            </td>
                            <td class="table-td">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($payment->status !== 'paid')
                                        <button type="button" wire:click="approve({{ $payment->id }})" wire:confirm="Approve this payment and mark the booking paid?" class="rounded p-1.5 text-muted hover:bg-emerald-50 hover:text-emerald-700" title="Approve">
                                            <x-icon name="check" class="h-4 w-4" />
                                        </button>
                                        <button type="button" wire:click="openReject({{ $payment->id }})" class="rounded p-1.5 text-muted hover:bg-red-50 hover:text-red-600" title="Reject">
                                            <x-icon name="x" class="h-4 w-4" />
                                        </button>
                                    @else
                                        <span class="text-[11px] text-muted">{{ $payment->verified_at?->format('j M') }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-muted">No payments match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line p-4">{{ $payments->links() }}</div>
    </div>

    @if ($rejectId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink/40 p-4">
            <div class="w-full max-w-sm rounded-card bg-white p-5 shadow-float">
                <h2 class="text-base font-bold">Reject payment</h2>
                <p class="mt-1 text-sm text-muted">The guest can upload a new proof while the booking is still pending.</p>
                <textarea wire:model="rejectReason" rows="3" placeholder="Why is this proof invalid?" class="field mt-3"></textarea>
                @error('rejectReason') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" wire:click="$set('rejectId', null)" class="btn-ghost border border-line">Cancel</button>
                    <button type="button" wire:click="reject" class="btn-primary bg-red-600 hover:bg-red-700">Reject payment</button>
                </div>
            </div>
        </div>
    @endif
</div>
