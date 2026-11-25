@extends('layouts.front')

@section('title', 'My bookings — NusaStay')

@section('content')
    <div class="border-b border-line bg-surface">
        <div class="mx-auto max-w-[1100px] px-4 py-5 sm:px-6">
            <h1 class="text-xl font-bold sm:text-2xl">My bookings</h1>
            <p class="mt-1 text-sm text-muted">Every reservation, payment status and invoice in one place.</p>
        </div>
    </div>

    <div class="mx-auto max-w-[1100px] px-4 py-6 sm:px-6">
        <livewire:front.my-booking-list />
    </div>
@endsection
