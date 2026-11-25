@extends('layouts.front')

@section('title', 'Profile — NusaStay')

@section('content')
    <div class="border-b border-line bg-surface">
        <div class="mx-auto max-w-[900px] px-4 py-5 sm:px-6">
            <h1 class="text-xl font-bold sm:text-2xl">Profile</h1>
            <p class="mt-1 text-sm text-muted">Update your details, password or close your account.</p>
        </div>
    </div>

    <div class="mx-auto max-w-[900px] space-y-5 px-4 py-6 sm:px-6">
        <div class="rounded-card border border-line bg-white p-5 sm:p-6">
            <livewire:profile.update-profile-information-form />
        </div>
        <div class="rounded-card border border-line bg-white p-5 sm:p-6">
            <livewire:profile.update-password-form />
        </div>
        <div class="rounded-card border border-line bg-white p-5 sm:p-6">
            <livewire:profile.delete-user-form />
        </div>
    </div>
@endsection
