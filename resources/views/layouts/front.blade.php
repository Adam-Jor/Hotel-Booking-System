<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $title ?? 'NusaStay — Hotels across Indonesia')</title>
    <meta name="description" content="@yield('meta', 'Find and book hotels across Indonesia. Compare rooms, prices and availability in one place.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-ink antialiased">
    @php($transparent = $transparent ?? false)

    <div x-data="{ open: false, account: false }" class="{{ $transparent ? 'absolute inset-x-0 top-0 z-40' : 'relative z-40 bg-white' }}">
        <header class="{{ $transparent ? 'text-white' : 'border-b border-line bg-white text-ink' }}">
            <div class="mx-auto flex h-16 max-w-[1240px] items-center gap-6 px-4 sm:px-6">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-1.5 text-xl font-extrabold tracking-tight">
                    <span class="{{ $transparent ? 'text-white' : 'text-brand-600' }}">nusa</span><span class="{{ $transparent ? 'text-white' : 'text-ink' }}">stay</span>
                    <svg class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.5 3.5 2.8 10.9c-.8.3-.8 1.5.1 1.7l6.4 1.8 1.8 6.4c.2.9 1.4.9 1.7.1L21.5 3.5Z"/></svg>
                </a>

                <div class="hidden items-center gap-1 text-sm font-semibold lg:flex">
                    <a href="{{ route('hotels.index') }}" wire:navigate class="rounded-control px-3 py-2 transition-colors {{ request()->routeIs('hotels.*') ? ($transparent ? 'bg-white/15' : 'bg-brand-50 text-brand-700') : 'hover:bg-white/10' }}">Hotels</a>
                    <a href="{{ route('hotels.index') }}" wire:navigate class="rounded-control px-3 py-2 transition-colors hover:bg-white/10">Deals</a>
                    <a href="{{ route('hotels.index') }}" wire:navigate class="rounded-control px-3 py-2 transition-colors hover:bg-white/10">Destinations</a>
                    @auth
                        <a href="{{ route('my-bookings') }}" wire:navigate class="rounded-control px-3 py-2 transition-colors hover:bg-white/10">My bookings</a>
                    @endauth
                </div>

                <div class="ml-auto flex items-center gap-2">
                    <div class="hidden items-center gap-2 sm:flex">
                        @auth
                            <div class="relative">
                                <button @click="account = !account" class="flex items-center gap-2 rounded-full py-1 pl-1 pr-3 text-sm font-semibold transition-colors {{ $transparent ? 'bg-white/10 hover:bg-white/20' : 'hover:bg-surface' }}">
                                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                                    <span class="max-w-[110px] truncate">{{ auth()->user()->name }}</span>
                                    <x-icon name="chevron-down" class="h-4 w-4" />
                                </button>
                                <div x-show="account" x-cloak @click.outside="account = false" class="absolute right-0 top-12 w-52 overflow-hidden rounded-card border border-line bg-white py-1.5 text-ink shadow-float">
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm font-medium hover:bg-surface">Admin dashboard</a>
                                    @endif
                                    <a href="{{ route('my-bookings') }}" class="block px-4 py-2 text-sm font-medium hover:bg-surface">My bookings</a>
                                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm font-medium hover:bg-surface">Profile</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50">Log out</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition-colors {{ $transparent ? 'bg-white text-brand-600 hover:bg-brand-50' : 'border border-brand-500 text-brand-600 hover:bg-brand-50' }}">
                                Log in
                            </a>
                            <a href="{{ route('register') }}" wire:navigate class="rounded-full bg-brand-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-600">Register</a>
                        @endauth
                    </div>
                    <button @click="open = !open" class="rounded-control p-2 lg:hidden" aria-label="Open menu">
                        <x-icon name="menu" class="h-6 w-6" />
                    </button>
                </div>
            </div>

            <div class="hidden border-t lg:block {{ $transparent ? 'border-white/15' : 'border-line' }}">
                <div class="mx-auto flex h-12 max-w-[1240px] items-center gap-6 px-6 text-sm font-semibold">
                    <a href="{{ route('hotels.index') }}" wire:navigate class="border-b-2 border-accent pb-1">Hotels</a>
                    <a href="{{ route('hotels.index') }}" wire:navigate class="opacity-80 transition-opacity hover:opacity-100">Villas</a>
                    <a href="{{ route('hotels.index') }}" wire:navigate class="opacity-80 transition-opacity hover:opacity-100">Apartments</a>
                    <a href="{{ route('hotels.index') }}" wire:navigate class="opacity-80 transition-opacity hover:opacity-100">Nearby stays</a>
                    <span class="ml-auto inline-flex items-center gap-2 text-xs font-medium {{ $transparent ? 'text-white/80' : 'text-muted' }}">
                        <x-icon name="shield" class="h-4 w-4" /> Secure booking
                    </span>
                </div>
            </div>
        </header>

        <div x-show="open" x-cloak class="border-b border-line bg-white px-4 py-3 text-ink lg:hidden">
            <nav class="flex flex-col gap-1 text-sm font-semibold">
                <a href="{{ route('hotels.index') }}" class="rounded-control px-3 py-2 hover:bg-surface">Hotels</a>
                @auth
                    <a href="{{ route('my-bookings') }}" class="rounded-control px-3 py-2 hover:bg-surface">My bookings</a>
                    <a href="{{ route('profile') }}" class="rounded-control px-3 py-2 hover:bg-surface">Profile</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="rounded-control px-3 py-2 hover:bg-surface">Admin dashboard</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="rounded-control px-3 py-2 hover:bg-surface">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-control px-3 py-2 hover:bg-surface">Register</a>
                @endauth
            </nav>
        </div>
    </div>

    <main>
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot ?? '' }}
        @endif
    </main>

    <footer class="mt-16 bg-brand-900 text-white">
        <div class="mx-auto grid max-w-[1240px] gap-10 px-6 py-12 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <div class="flex items-center gap-1.5 text-xl font-extrabold tracking-tight">
                    nusastay
                    <svg class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.5 3.5 2.8 10.9c-.8.3-.8 1.5.1 1.7l6.4 1.8 1.8 6.4c.2.9 1.4.9 1.7.1L21.5 3.5Z"/></svg>
                </div>
                <p class="mt-3 max-w-xs text-sm leading-relaxed text-white/70">Book hotels across Indonesia with clear prices, real availability and no surprises at the front desk.</p>
            </div>
            <div>
                <p class="text-sm font-semibold">Explore</p>
                <ul class="mt-3 space-y-2 text-sm text-white/70">
                    <li><a href="{{ route('hotels.index') }}" class="hover:text-white">All hotels</a></li>
                    <li><a href="{{ route('hotels.index', ['city' => 'Bali']) }}" class="hover:text-white">Bali</a></li>
                    <li><a href="{{ route('hotels.index', ['city' => 'Jakarta']) }}" class="hover:text-white">Jakarta</a></li>
                    <li><a href="{{ route('hotels.index', ['city' => 'Yogyakarta']) }}" class="hover:text-white">Yogyakarta</a></li>
                </ul>
            </div>
            <div>
                <p class="text-sm font-semibold">Support</p>
                <ul class="mt-3 space-y-2 text-sm text-white/70">
                    <li>Help centre</li>
                    <li>Booking policy</li>
                    <li>Cancellation</li>
                    <li>Privacy</li>
                </ul>
            </div>
            <div>
                <p class="text-sm font-semibold">Contact</p>
                <ul class="mt-3 space-y-2 text-sm text-white/70">
                    <li>hello@nusastay.test</li>
                    <li>+62 21 5000 1234</li>
                    <li>Jakarta, Indonesia</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="mx-auto flex max-w-[1240px] flex-col gap-2 px-6 py-5 text-xs text-white/60 sm:flex-row sm:items-center sm:justify-between">
                <p>© {{ date('Y') }} NusaStay. All rights reserved.</p>
                <p>Prices in IDR. Demo booking system.</p>
            </div>
        </div>
    </footer>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-cloak
             class="fixed bottom-5 left-1/2 z-50 flex -translate-x-1/2 items-center gap-2 rounded-card bg-ink px-4 py-3 text-sm font-medium text-white shadow-float">
            <x-icon name="check" class="h-4 w-4 text-emerald-400" />
            {{ session('success') }}
        </div>
    @endif
</body>
</html>
