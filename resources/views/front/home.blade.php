@extends('layouts.front')

@php($transparent = true)

@section('title', 'NusaStay — Hotels across Indonesia')

@section('content')
    <section class="relative overflow-hidden bg-brand-900">
        <img src="https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=2000&q=75"
             alt="Coastal resort in Indonesia" class="absolute inset-0 h-full w-full object-cover" width="2000" height="1100">
        <div class="absolute inset-0 bg-gradient-to-b from-ink/70 via-ink/35 to-ink/60"></div>

        <div class="relative mx-auto max-w-[1240px] px-4 pb-14 pt-32 sm:px-6 sm:pb-16 lg:pt-36">
            <div class="mx-auto max-w-3xl text-center text-white">
                <p class="text-xs font-semibold uppercase tracking-wide text-white/75">Hotels · Villas · City stays</p>
                <h1 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl lg:text-[42px]">Find a room worth waking up in</h1>
                <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-white/80 sm:text-base">
                    Compare hotels across Indonesia with real availability and clear nightly prices. Book in a few steps.
                </p>
            </div>

            <div class="mx-auto mt-8 max-w-4xl">
                <livewire:front.search-form />
            </div>

            <div class="mx-auto mt-6 flex max-w-4xl flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs text-white/75">
                <span class="inline-flex items-center gap-1.5"><x-icon name="shield" class="h-4 w-4" /> Verified hotels</span>
                <span class="inline-flex items-center gap-1.5"><x-icon name="clock" class="h-4 w-4" /> 60-minute room hold</span>
                <span class="inline-flex items-center gap-1.5"><x-icon name="download" class="h-4 w-4" /> Invoice after payment</span>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-[1240px] px-4 py-12 sm:px-6">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-card border border-line bg-white p-5">
                <div class="flex h-9 w-9 items-center justify-center rounded-control bg-brand-50 text-brand-600"><x-icon name="map-pin" class="h-4 w-4" /></div>
                <p class="mt-3 text-sm font-bold">{{ $stats['hotels'] }} hotels in {{ $stats['cities'] }} cities</p>
                <p class="mt-1 text-sm text-muted">From beach resorts in Bali to business stays in Jakarta.</p>
            </div>
            <div class="rounded-card border border-line bg-white p-5">
                <div class="flex h-9 w-9 items-center justify-center rounded-control bg-brand-50 text-brand-600"><x-icon name="calendar" class="h-4 w-4" /></div>
                <p class="mt-3 text-sm font-bold">Availability by date</p>
                <p class="mt-1 text-sm text-muted">Only rooms that are actually free for your nights appear.</p>
            </div>
            <div class="rounded-card border border-line bg-white p-5">
                <div class="flex h-9 w-9 items-center justify-center rounded-control bg-brand-50 text-brand-600"><x-icon name="credit-card" class="h-4 w-4" /></div>
                <p class="mt-3 text-sm font-bold">Pay by transfer or e-wallet</p>
                <p class="mt-1 text-sm text-muted">Simulated payment with a clear status at every step.</p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-[1240px] px-4 pb-4 sm:px-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold sm:text-2xl">Popular destinations</h2>
                <p class="mt-1 text-sm text-muted">Where travellers are booking this month.</p>
            </div>
            <a href="{{ route('hotels.index') }}" wire:navigate class="hidden text-sm font-semibold text-brand-600 hover:text-brand-700 sm:inline-flex">See all hotels</a>
        </div>

        @php($cityImages = [
            'Bali' => 'photo-1537996194471-e657df975ab4',
            'Jakarta' => 'photo-1555899434-94d1368aa7af',
            'Bandung' => 'photo-1509474520651-53cf6a80f2b7',
            'Yogyakarta' => 'photo-1596422846543-75c6fc197f07',
            'Surabaya' => 'photo-1564501049412-61c2a3083791',
            'Lombok' => 'photo-1518548419970-58e3b4079ab2',
            'Labuan Bajo' => 'photo-1516690561799-46d8f74f9abf',
            'Medan' => 'photo-1445019980597-93fa8acb246c',
        ])

        <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($cities as $city)
                <a href="{{ route('hotels.index', ['city' => $city->city]) }}" wire:navigate class="group relative overflow-hidden rounded-card bg-brand-900">
                    <img src="https://images.unsplash.com/{{ $cityImages[$city->city] ?? 'photo-1566073771259-6a8506099945' }}?auto=format&fit=crop&w=500&q=70"
                         alt="{{ $city->city }}" loading="lazy" width="300" height="360" class="h-40 w-full object-cover opacity-80 transition-transform duration-300 group-hover:scale-[1.03]">
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink/85 to-transparent p-3 text-white">
                        <p class="text-sm font-bold">{{ $city->city }}</p>
                        <p class="text-[11px] text-white/75">{{ $city->hotels }} hotels</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-[1240px] px-4 py-10 sm:px-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold sm:text-2xl">Recommended stays</h2>
                <p class="mt-1 text-sm text-muted">Top-rated hotels with rooms ready to book.</p>
            </div>
            <a href="{{ route('hotels.index') }}" wire:navigate class="text-sm font-semibold text-brand-600 hover:text-brand-700">Browse all</a>
        </div>

        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($hotels as $hotel)
                <x-front.hotel-card :hotel="$hotel" />
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-[1240px] px-4 pb-14 sm:px-6">
        <div class="overflow-hidden rounded-card bg-brand-50">
            <div class="grid items-center gap-6 p-6 sm:p-8 lg:grid-cols-[1.4fr_1fr]">
                <div>
                    <h2 class="text-xl font-bold sm:text-2xl">Book direct, keep the details</h2>
                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-muted">
                        Your booking code, payment status and invoice live in one account. Cancel a pending booking yourself, or pick up where you left off.
                    </p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('hotels.index') }}" wire:navigate class="btn-primary">Find a hotel</a>
                        @guest
                            <a href="{{ route('register') }}" wire:navigate class="btn-secondary">Create account</a>
                        @endguest
                    </div>
                </div>
                <div class="rounded-card bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-muted">Booking summary</p>
                    <div class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-muted">Room</span><span class="font-semibold">Deluxe · 2 nights</span></div>
                        <div class="flex justify-between"><span class="text-muted">Check-in</span><span class="font-semibold">1 Feb 2026</span></div>
                        <div class="flex justify-between"><span class="text-muted">Total</span><span class="font-bold text-accent">Rp 1.700.000</span></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2 rounded-control bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
                        <x-icon name="check" class="h-4 w-4" /> Paid · invoice ready
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
