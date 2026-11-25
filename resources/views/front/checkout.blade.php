@extends('layouts.front')

@section('title', 'Checkout — NusaStay')

@section('content')
    <div class="border-b border-line bg-surface">
        <div class="mx-auto max-w-[1240px] px-4 py-4 sm:px-6">
            <nav class="flex flex-wrap items-center gap-1.5 text-xs text-muted">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-ink">Home</a>
                <span>/</span>
                <a href="{{ route('hotels.show', $room->hotel->slug) }}" wire:navigate class="hover:text-ink">{{ $room->hotel->name }}</a>
                <span>/</span>
                <span class="font-semibold text-ink">Checkout</span>
            </nav>
        </div>
    </div>

    <div class="mx-auto max-w-[1100px] px-4 py-6 sm:px-6">
        <h1 class="text-xl font-bold sm:text-2xl">Complete your booking</h1>
        <p class="mt-1 text-sm text-muted">Three short steps. Your room is held for 60 minutes once you pay.</p>

        <div class="mt-5">
            <livewire:front.checkout-wizard :room="$room" />
        </div>
    </div>
@endsection
