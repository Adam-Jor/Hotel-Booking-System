@php
    $steps = [1 => 'Summary', 2 => 'Guest details', 3 => 'Payment', 4 => 'Pay & confirm'];
@endphp

<div class="grid gap-6 lg:grid-cols-[1fr_340px]">
    <div class="min-w-0">
        <ol class="mb-5 flex flex-wrap items-center gap-2 text-xs font-semibold">
            @foreach ($steps as $number => $label)
                <li class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full {{ $step >= $number ? 'bg-brand-500 text-white' : 'bg-surface text-muted' }}">
                        @if ($step > $number)
                            <x-icon name="check" class="h-3.5 w-3.5" />
                        @else
                            {{ $number }}
                        @endif
                    </span>
                    <span class="{{ $step >= $number ? 'text-ink' : 'text-muted' }}">{{ $label }}</span>
                    @unless ($loop->last)
                        <span class="mx-1 hidden h-px w-6 bg-line sm:block"></span>
                    @endunless
                </li>
            @endforeach
        </ol>

        @error('booking')
            <div class="mb-4 rounded-card border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ $message }}</div>
        @enderror

        <div class="rounded-card border border-line bg-white p-5">
            @if ($step === 1)
                <h2 class="text-base font-bold">Your stay</h2>
                <p class="mt-1 text-sm text-muted">{{ $room->hotel->name }} · {{ $room->roomType->name }}</p>

                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                    <div>
                        <p class="label">Check-in</p>
                        <input wire:model.live="check_in" type="date" min="{{ now()->toDateString() }}" class="field">
                        @error('check_in') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <p class="label">Check-out</p>
                        <input wire:model.live="check_out" type="date" min="{{ now()->addDay()->toDateString() }}" class="field">
                        @error('check_out') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <p class="label">Guests</p>
                        <select wire:model.live="guests" class="field">
                            @for ($i = 1; $i <= $room->roomType->capacity; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'guest' : 'guests' }}</option>
                            @endfor
                        </select>
                        @error('guests') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-3 rounded-control bg-surface px-4 py-3 text-sm">
                    <x-icon name="clock" class="h-4 w-4 text-brand-600" />
                    {{ $nights }} {{ $nights === 1 ? 'night' : 'nights' }} · Rp {{ number_format((float) $room->price, 0, ',', '.') }} per night
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="button" wire:click="next" class="btn-primary">Continue to guest details</button>
                </div>
            @elseif ($step === 2)
                <h2 class="text-base font-bold">Guest details</h2>
                <p class="mt-1 text-sm text-muted">We use these details for the booking and WhatsApp updates.</p>

                <div class="mt-5 space-y-4">
                    <div>
                        <label class="label">Guest name <span class="text-accent">*</span></label>
                        <input wire:model="customer_name" type="text" placeholder="Name on the booking" class="field">
                        @error('customer_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">WhatsApp number <span class="text-accent">*</span></label>
                        <input wire:model="phone" type="text" placeholder="6281234567890" class="field">
                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label">Special requests</label>
                        <textarea wire:model="notes" rows="3" placeholder="Late check-in, high floor, extra pillow…" class="field"></textarea>
                        @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-5 flex justify-between">
                    <button type="button" wire:click="back" class="btn-ghost border border-line">Back</button>
                    <button type="button" wire:click="next" class="btn-primary">Continue to payment</button>
                </div>
            @elseif ($step === 3)
                <h2 class="text-base font-bold">Payment method</h2>
                <p class="mt-1 text-sm text-muted">Both methods are simulated. No real money moves.</p>

                <div class="mt-5 space-y-3">
                    <label class="flex cursor-pointer items-start gap-3 rounded-card border p-4 {{ $payment_method === 'bank_transfer' ? 'border-brand-500 bg-brand-50/50' : 'border-line' }}">
                        <input type="radio" wire:model.live="payment_method" value="bank_transfer" class="mt-0.5 border-line text-brand-500 focus:ring-brand-500">
                        <span>
                            <span class="block text-sm font-bold">Bank transfer</span>
                            <span class="mt-0.5 block text-xs text-muted">Get a virtual account number, upload your proof, and wait for verification.</span>
                        </span>
                    </label>
                    <label class="flex cursor-pointer items-start gap-3 rounded-card border p-4 {{ $payment_method === 'ewallet_sim' ? 'border-brand-500 bg-brand-50/50' : 'border-line' }}">
                        <input type="radio" wire:model.live="payment_method" value="ewallet_sim" class="mt-0.5 border-line text-brand-500 focus:ring-brand-500">
                        <span>
                            <span class="block text-sm font-bold">E-wallet (instant)</span>
                            <span class="mt-0.5 block text-xs text-muted">Simulated QR payment. Confirmed automatically in a moment.</span>
                        </span>
                    </label>
                </div>
                @error('payment_method') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror

                <div class="mt-5 flex justify-between">
                    <button type="button" wire:click="back" class="btn-ghost border border-line">Back</button>
                    <button type="button" wire:click="pay" class="btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="pay">Pay Rp {{ number_format($total, 0, ',', '.') }}</span>
                        <span wire:loading wire:target="pay">Creating booking…</span>
                    </button>
                </div>
            @elseif ($step === 4 && $booking)
                @php($isPaid = $booking->status === 'paid')
                @php($isExpired = $booking->status === 'cancelled' || ($booking->expires_at && $booking->expires_at->isPast()))

                @if ($isPaid)
                    <div class="rounded-card bg-emerald-50 p-5 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-600 text-white"><x-icon name="check" class="h-6 w-6" /></div>
                        <h2 class="mt-3 text-lg font-bold text-emerald-900">Booking confirmed</h2>
                        <p class="mt-1 text-sm text-emerald-800">Payment received. Your booking code is <span class="font-bold">{{ $booking->booking_code }}</span>.</p>
                        <div class="mt-4 flex flex-wrap justify-center gap-2">
                            <a href="{{ route('bookings.invoice', $booking) }}" class="btn-primary"><x-icon name="download" class="h-4 w-4" /> Download invoice</a>
                            <a href="{{ route('my-bookings') }}" wire:navigate class="btn-secondary">My bookings</a>
                        </div>
                    </div>
                @elseif ($isExpired)
                    <div class="rounded-card bg-red-50 p-5 text-center">
                        <h2 class="text-lg font-bold text-red-800">This hold has expired</h2>
                        <p class="mt-1 text-sm text-red-700">The room was released. Create a new booking to try again.</p>
                        <a href="{{ route('hotels.show', $room->hotel->slug) }}" wire:navigate class="btn-primary mt-4">Back to hotel</a>
                    </div>
                @else
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold">Booking {{ $booking->booking_code }}</h2>
                            <p class="mt-0.5 text-sm text-muted">{{ $booking->status === 'pending' ? 'Waiting for payment' : ucfirst($booking->status) }}</p>
                        </div>
                        <div class="rounded-control bg-accent-soft px-3 py-2 text-right">
                            <p class="text-[11px] font-semibold text-accent-dark">Pay before the hold ends</p>
                            <p class="text-base font-extrabold text-accent-dark"
                               x-data="{ left: Math.max(0, {{ $booking->expires_at?->timestamp ?? 0 }} - Math.floor(Date.now()/1000)) }"
                               x-init="setInterval(() => { if (left > 0) left-- }, 1000)">
                                <span x-text="String(Math.floor(left/60)).padStart(2,'0') + ':' + String(left%60).padStart(2,'0')"></span>
                            </p>
                        </div>
                    </div>

                    @if ($payment_method === 'bank_transfer' || $payment->payment_method === 'bank_transfer')
                        <div class="mt-4 rounded-card border border-line p-4">
                            <p class="text-sm font-bold">Transfer to this virtual account</p>
                            <div class="mt-2 flex flex-wrap items-center justify-between gap-2 rounded-control bg-surface px-4 py-3">
                                <div>
                                    <p class="text-[11px] text-muted">Bank Mandiri (simulated) · {{ $payment->reference_code }}</p>
                                    <p class="text-lg font-extrabold tracking-wide">8808 {{ substr($payment->reference_code, -4) }} {{ substr($payment->reference_code, -8, 4) }}</p>
                                </div>
                                <p class="text-base font-extrabold text-accent">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                            </div>
                            <p class="mt-2 text-xs text-muted">Upload a JPG, PNG or PDF up to 2 MB. An admin verifies it, then the booking becomes paid.</p>
                        </div>

                        @if ($payment->proof_url && $payment->status === 'pending')
                            <div class="mt-4 rounded-card bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">
                                Proof uploaded. Waiting for admin verification.
                            </div>
                        @else
                            <div class="mt-4">
                                <label class="label">Payment proof</label>
                                <input type="file" wire:model="proof" accept=".jpg,.jpeg,.png,.pdf" class="field py-2">
                                @error('proof') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                <div wire:loading wire:target="proof" class="mt-2 text-xs text-muted">Uploading…</div>
                                @if ($proof && ! str_ends_with(strtolower($proof->getClientOriginalName()), '.pdf'))
                                    <img src="{{ $proof->temporaryUrl() }}" alt="Proof preview" class="mt-2 h-32 rounded-control object-cover">
                                @endif
                                <button type="button" wire:click="uploadProof" class="btn-primary mt-3" wire:loading.attr="disabled">Upload proof</button>
                            </div>
                        @endif
                    @else
                        <div class="mt-4 rounded-card border border-line p-4">
                            <p class="text-sm font-bold">Scan to pay (simulated)</p>
                            <div class="mt-3 flex flex-col items-center gap-3 sm:flex-row">
                                <div class="grid h-32 w-32 grid-cols-5 gap-1 rounded-control bg-white p-2 ring-1 ring-line">
                                    @for ($i = 0; $i < 25; $i++)
                                        <span class="{{ ($i * 7 + 3) % 3 === 0 ? 'bg-ink' : 'bg-transparent' }} rounded-[2px]"></span>
                                    @endfor
                                </div>
                                <div class="text-sm">
                                    <p class="font-semibold">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                                    <p class="mt-1 text-xs text-muted">Reference {{ $payment->reference_code }}</p>
                                    <button type="button" wire:click="payNow" class="btn-primary mt-3" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="payNow">Pay now</span>
                                        <span wire:loading wire:target="payNow">Confirming…</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>

    <aside>
        <div class="sticky top-4 rounded-card border border-line bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-muted">Booking summary</p>
            <img src="{{ $room->image }}" alt="{{ $room->hotel->name }}" class="mt-3 h-28 w-full rounded-control object-cover">
            <div class="mt-3">
                <p class="text-sm font-bold">{{ $room->hotel->name }}</p>
                <p class="text-xs text-muted">{{ $room->city ?? $room->hotel->city }} · {{ $room->roomType->name }}</p>
            </div>
            <dl class="mt-4 space-y-2 border-t border-line pt-3 text-sm">
                <div class="flex justify-between"><dt class="text-muted">Check-in</dt><dd class="font-semibold">{{ \Illuminate\Support\Carbon::parse($check_in)->format('j M Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-muted">Check-out</dt><dd class="font-semibold">{{ \Illuminate\Support\Carbon::parse($check_out)->format('j M Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-muted">Nights</dt><dd class="font-semibold">{{ $nights }}</dd></div>
                <div class="flex justify-between"><dt class="text-muted">Guests</dt><dd class="font-semibold">{{ $guests }}</dd></div>
                <div class="flex justify-between"><dt class="text-muted">Room price</dt><dd class="font-semibold">Rp {{ number_format((float) $room->price, 0, ',', '.') }}</dd></div>
            </dl>
            <div class="mt-4 flex items-center justify-between border-t border-line pt-3">
                <span class="text-sm font-semibold">Total</span>
                <span class="text-lg font-extrabold text-accent">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            @if ($booking)
                <p class="mt-3 text-center text-xs text-muted">Booking code <span class="font-bold text-ink">{{ $booking->booking_code }}</span></p>
            @endif
        </div>
    </aside>
</div>
