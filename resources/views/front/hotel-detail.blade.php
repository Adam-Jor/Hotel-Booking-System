@extends('layouts.front')

@section('title', $hotel->name.' — NusaStay')
@section('meta', \Illuminate\Support\Str::limit($hotel->description, 150))

@section('content')
    <div class="border-b border-line bg-surface">
        <div class="mx-auto max-w-[1240px] px-4 py-4 sm:px-6">
            <nav class="flex flex-wrap items-center gap-1.5 text-xs text-muted">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-ink">Home</a>
                <span>/</span>
                <a href="{{ route('hotels.index') }}" wire:navigate class="hover:text-ink">Hotels</a>
                <span>/</span>
                <span class="font-semibold text-ink">{{ $hotel->name }}</span>
            </nav>
        </div>
    </div>

    <div class="mx-auto max-w-[1240px] px-4 py-6 sm:px-6">
        <div class="grid gap-2 overflow-hidden rounded-card sm:grid-cols-[1.6fr_1fr]">
            <img src="{{ $hotel->image }}" alt="{{ $hotel->name }}" width="1200" height="700" class="h-64 w-full object-cover sm:h-[380px]">
            <div class="hidden grid-rows-2 gap-2 sm:grid">
                @foreach ($hotel->images->skip(1)->take(2) as $image)
                    <img src="{{ $image->img_url }}" alt="{{ $hotel->name }} photo {{ $loop->iteration }}" loading="lazy" width="600" height="350" class="h-full w-full object-cover">
                @endforeach
            </div>
        </div>

        <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_360px]">
            <div class="min-w-0">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-1.5 text-accent">
                            @for ($i = 0; $i < ($hotel->star_rating ?? 0); $i++)
                                <x-icon name="star" class="h-4 w-4 fill-accent text-accent" />
                            @endfor
                            <span class="ml-1 text-xs font-semibold text-muted">{{ $hotel->star_rating }}-star hotel</span>
                        </div>
                        <h1 class="mt-1.5 text-2xl font-extrabold sm:text-3xl">{{ $hotel->name }}</h1>
                        <p class="mt-1 flex items-center gap-1.5 text-sm text-muted">
                            <x-icon name="map-pin" class="h-4 w-4 text-brand-600" />
                            {{ $hotel->address }}
                        </p>
                    </div>
                    @if ($rating > 0)
                        <div class="rounded-card bg-brand-50 px-3 py-2 text-right">
                            <p class="text-lg font-extrabold text-brand-700">{{ number_format($rating, 1) }}</p>
                            <p class="text-[11px] font-semibold text-brand-700/80">{{ $hotel->reviews->count() }} reviews</p>
                        </div>
                    @endif
                </div>

                @if ($hotel->facilities->isNotEmpty())
                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach ($hotel->facilities as $facility)
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-line bg-white px-3 py-1.5 text-xs font-medium text-muted">
                                <x-icon :name="$facility->icon ?: 'check'" class="h-3.5 w-3.5 text-brand-600" />
                                {{ $facility->name }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="mt-6 border-t border-line pt-5">
                    <h2 class="text-base font-bold">About this hotel</h2>
                    <p class="mt-2 text-sm leading-relaxed text-muted">{{ $hotel->description }}</p>
                </div>

                <div class="mt-8" id="rooms">
                    <div class="flex items-end justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold">Available rooms</h2>
                            <p class="mt-1 text-sm text-muted">Prices are per night and update with your dates.</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <livewire:front.room-availability :hotel-id="$hotel->id" />
                    </div>
                </div>

                <div class="mt-10 border-t border-line pt-6">
                    <h2 class="text-lg font-bold">Guest reviews</h2>
                    @forelse ($hotel->reviews as $review)
                        <div class="mt-4 rounded-card border border-line bg-white p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $review->user?->avatar_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                                    <div>
                                        <p class="text-sm font-semibold">{{ $review->user?->name }}</p>
                                        <p class="text-[11px] text-muted">{{ $review->created_at->format('j M Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-0.5 text-accent">
                                    @for ($i = 0; $i < $review->rating; $i++)
                                        <x-icon name="star" class="h-3.5 w-3.5 fill-accent text-accent" />
                                    @endfor
                                </div>
                            </div>
                            @if ($review->comment)
                                <p class="mt-2 text-sm text-muted">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="mt-3 text-sm text-muted">No reviews yet. Be the first to stay here.</p>
                    @endforelse
                </div>
            </div>

            <aside class="hidden lg:block">
                <div class="sticky top-4 space-y-4">
                    <div class="rounded-card border border-line bg-white p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-muted">Why book here</p>
                        <ul class="mt-3 space-y-3 text-sm">
                            <li class="flex items-start gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" /> Live availability for your dates</li>
                            <li class="flex items-start gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" /> Room held for 60 minutes after booking</li>
                            <li class="flex items-start gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" /> Invoice PDF after payment</li>
                            <li class="flex items-start gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" /> Cancel a pending booking yourself</li>
                        </ul>
                        <a href="#rooms" class="btn-primary mt-5 w-full">See rooms</a>
                    </div>
                    <div class="rounded-card bg-surface p-5">
                        <p class="text-sm font-bold">Need a hand?</p>
                        <p class="mt-1 text-sm text-muted">Our team answers every day from 08:00 to 22:00 WIB.</p>
                        <p class="mt-2 text-sm font-semibold text-brand-600">hello@nusastay.test</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
