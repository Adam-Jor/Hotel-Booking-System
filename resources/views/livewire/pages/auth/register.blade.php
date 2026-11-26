<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9._-]+$/', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20', 'regex:/^62[0-9]{8,13}$/'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone.regex' => 'Use WhatsApp format starting with 62, for example 6281234567890.',
        ]);

        $validated['role'] = 'customer';
        $validated['status'] = 1;

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('home', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h1 class="text-2xl font-bold tracking-tight">Create your account</h1>
    <p class="mt-1.5 text-sm text-muted">Book hotels and keep every reservation in one place.</p>

    <form wire:submit="register" class="mt-6 space-y-4">
        <div>
            <label for="name" class="label">Full name <span class="text-accent">*</span></label>
            <input wire:model="name" id="name" type="text" required autofocus autocomplete="name" placeholder="Rina Wijaya" class="field">
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="username" class="label">Username <span class="text-accent">*</span></label>
                <input wire:model="username" id="username" type="text" required autocomplete="username" placeholder="rina" class="field">
                <x-input-error :messages="$errors->get('username')" class="mt-1.5" />
            </div>
            <div>
                <label for="phone" class="label">WhatsApp <span class="text-accent">*</span></label>
                <input wire:model="phone" id="phone" type="text" required autocomplete="tel" placeholder="6281234567890" class="field">
                <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
            </div>
        </div>

        <div>
            <label for="email" class="label">Email <span class="text-accent">*</span></label>
            <input wire:model="email" id="email" type="email" required autocomplete="email" placeholder="you@example.com" class="field">
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="label">Password <span class="text-accent">*</span></label>
                <div x-data="{ show: false }" class="relative">
                    <input wire:model="password" id="password" :type="show ? 'text' : 'password'" required autocomplete="new-password" placeholder="Min. 8 characters" class="field pr-12">
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-ink"
                            aria-label="Toggle password visibility">
                        <x-icon name="eye" class="h-5 w-5" x-show="!show" />
                        <x-icon name="eye-off" class="h-5 w-5" x-show="show" />
                    </button>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>
            </div>
            <div>
                <label for="password_confirmation" class="label">Confirm password <span class="text-accent">*</span></label>
                <div x-data="{ show: false }" class="relative">
                    <input wire:model="password_confirmation" id="password_confirmation" :type="show ? 'text' : 'password'" required autocomplete="new-password" placeholder="Repeat password" class="field pr-12">
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-ink"
                            aria-label="Toggle password visibility">
                        <x-icon name="eye" class="h-5 w-5" x-show="!show" />
                        <x-icon name="eye-off" class="h-5 w-5" x-show="show" />
                    </button>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="register">Create account</span>
            <span wire:loading wire:target="register">Creating account…</span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        Already have an account?
        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-brand-600 hover:text-brand-700">Log in</a>
    </p>
</div>
