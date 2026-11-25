<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $target = auth()->user()->isAdmin()
            ? route('admin.dashboard', absolute: false)
            : route('home', absolute: false);

        $this->redirectIntended(default: $target, navigate: true);
    }
}; ?>

<div>
    <h1 class="text-2xl font-bold tracking-tight">Log in to NusaStay</h1>
    <p class="mt-1.5 text-sm text-muted">Manage your bookings and keep your trip in one place.</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form wire:submit="login" class="mt-6 space-y-4">
        <div>
            <label for="login" class="label">Email or username <span class="text-accent">*</span></label>
            <input wire:model="form.login" id="login" type="text" name="login" required autofocus autocomplete="username"
                   placeholder="you@example.com or username" class="field">
            <x-input-error :messages="$errors->get('form.login')" class="mt-1.5" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="label">Password <span class="text-accent">*</span></label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-brand-600 hover:text-brand-700" href="{{ route('password.request') }}" wire:navigate>Forgot password?</a>
                @endif
            </div>
            <input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password"
                   placeholder="Your password" class="field">
            <x-input-error :messages="$errors->get('form.password')" class="mt-1.5" />
        </div>

        <label for="remember" class="flex items-center gap-2 text-sm text-muted">
            <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-line text-brand-500 focus:ring-brand-500">
            Keep me logged in
        </label>

        <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="login">Log in</span>
            <span wire:loading wire:target="login">Logging in…</span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        New to NusaStay?
        <a href="{{ route('register') }}" wire:navigate class="font-semibold text-brand-600 hover:text-brand-700">Create an account</a>
    </p>

    <div class="mt-6 rounded-card bg-surface p-3 text-xs text-muted">
        Demo accounts — admin: <span class="font-semibold text-ink">admin@hotel.test</span> · customer: <span class="font-semibold text-ink">customer@hotel.test</span> · password: <span class="font-semibold text-ink">password</span>
    </div>
</div>
