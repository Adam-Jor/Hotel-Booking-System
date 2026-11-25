@extends('layouts.front')

@section('title', 'Search hotels — NusaStay')

@section('content')
    <div class="border-b border-line bg-surface">
        <div class="mx-auto max-w-[1240px] px-4 py-5 sm:px-6">
            <nav class="flex items-center gap-1.5 text-xs text-muted">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-ink">Home</a>
                <span>/</span>
                <span class="font-semibold text-ink">Hotels</span>
            </nav>
            <div class="mt-2 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-xl font-bold sm:text-2xl">Hotels{{ request('city') ? ' in '.request('city') : ' across Indonesia' }}</h1>
                    <p class="mt-1 text-sm text-muted">Compare rooms with live availability for your dates.</p>
                </div>
                <div class="flex items-center gap-2 text-xs text-muted">
                    <x-icon name="shield" class="h-4 w-4 text-brand-600" /> Availability checked per date
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-[1240px] px-4 py-6 sm:px-6">
        <livewire:front.hotel-list />
    </div>
@endsection
