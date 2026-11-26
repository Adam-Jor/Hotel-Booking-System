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
    <header x-data="{ mobileMenu: false, accountMenu: false }"
            class="relative z-40 sticky top-0 border-b border-line bg-white/95 text-ink backdrop-blur-sm">
        <div class="mx-auto flex h-16 max-w-[1240px] items-center gap-4 px-4 sm:px-6">
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-1.5 text-xl font-extrabold tracking-tight flex-shrink-0">
                <span class="text-brand-600">nusa</span><span class="text-ink">stay</span>
                <svg class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.5 3.5 2.8 10.9c-.8.3-.8 1.5.1 1.7l6.4 1.8 1.8 6.4c.2.9 1.4.9 1.7.1L21.5 3.5Z"/></svg>
            </a>

            <nav class="hidden md:flex items-center gap-1 ml-4 flex-1 max-w-[500px]" aria-label="Main navigation">
                <a href="{{ route('hotels.index') }}"
                   wire:navigate
                   class="rounded-control px-3 py-2 text-sm font-semibold transition-colors
                          {{ request()->routeIs('hotels.*')
                              ? 'bg-brand-50 text-brand-700'
                              : 'hover:bg-surface' }}">
                    Hotels
                </a>
                @auth
                    <a href="{{ route('my-bookings') }}"
                       wire:navigate
                       class="rounded-control px-3 py-2 text-sm font-semibold transition-colors
                              {{ request()->routeIs('my-bookings')
                                  ? 'bg-brand-50 text-brand-700'
                                  : 'hover:bg-surface' }}">
                        My bookings
                    </a>
                @endauth
            </nav>

            <div class="flex items-center gap-2 ml-auto">
                <div class="hidden md:flex items-center gap-2">
                    @auth
                        <div class="relative">
                            <button @click="accountMenu = !accountMenu"
                                    @keydown.escape="accountMenu = false"
                                    class="flex items-center gap-2 rounded-full py-1 pl-1 pr-3 text-sm font-semibold transition-colors hover:bg-surface"
                                    aria-expanded="false"
                                    aria-haspopup="true">
                                <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0194F3&color=fff' }}"
                                     alt=""
                                     class="h-8 w-8 rounded-full object-cover">
                                <span class="max-w-[110px] truncate">{{ auth()->user()->name }}</span>
                                <span class="transition-transform duration-200" :class="{ 'rotate-180': accountMenu }">
                                <x-icon name="chevron-down" class="h-4 w-4" />
                            </span>
                            </button>
                            <div x-show="accountMenu"
                                 x-cloak
                                 @click.outside="accountMenu = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 transform -translate-y-1"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-1"
                                 class="absolute right-0 top-full mt-1.5 w-56 overflow-hidden rounded-card border border-line bg-white py-1.5 shadow-float">
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}"
                                       wire:navigate
                                       class="block px-4 py-2 text-sm font-medium text-ink hover:bg-surface">
                                        Admin dashboard
                                    </a>
                                @endif
                                <a href="{{ route('my-bookings') }}"
                                   wire:navigate
                                   class="block px-4 py-2 text-sm font-medium text-ink hover:bg-surface">
                                    My bookings
                                </a>
                                <a href="{{ route('profile') }}"
                                   wire:navigate
                                   class="block px-4 py-2 text-sm font-medium text-ink hover:bg-surface">
                                    Profile
                                </a>
                                <hr class="my-1.5 border-line">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="block w-full px-4 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50">
                                        Log out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                           wire:navigate
                           class="inline-flex items-center gap-2 rounded-full border border-brand-500 px-4 py-2 text-sm font-semibold text-brand-600 transition-colors hover:bg-brand-50">
                            Log in
                        </a>
                        <a href="{{ route('register') }}"
                           wire:navigate
                           class="rounded-full bg-brand-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-600">
                            Register
                        </a>
                    @endauth
                </div>

                <button @click="mobileMenu = !mobileMenu"
                        @keydown.escape="mobileMenu = false"
                        class="md:hidden rounded-control p-2 text-ink hover:bg-surface transition-colors"
                        aria-label="Open menu"
                        aria-expanded="false">
                    <x-icon name="x" class="h-6 w-6" x-show="mobileMenu" />
                    <x-icon name="menu" class="h-6 w-6" x-show="!mobileMenu" />
                </button>
            </div>
        </div>

        <div x-show="mobileMenu"
             x-cloak
             @click.outside="mobileMenu = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-4"
             class="md:hidden absolute inset-x-0 top-full z-40 max-h-[calc(100vh-4rem)] overflow-y-auto border-b border-line bg-white shadow-float"
             role="dialog"
             aria-modal="true"
             aria-label="Mobile menu">
            <nav class="px-4 py-4 space-y-2">
                <a href="{{ route('hotels.index') }}"
                   wire:navigate
                   @click="mobileMenu = false"
                   class="block rounded-control px-3 py-2 text-base font-semibold text-ink hover:bg-surface">
                    Hotels
                </a>
                @auth
                    <a href="{{ route('my-bookings') }}"
                       wire:navigate
                       @click="mobileMenu = false"
                       class="block rounded-control px-3 py-2 text-base font-semibold text-ink hover:bg-surface">
                        My bookings
                    </a>
                    <a href="{{ route('profile') }}"
                       wire:navigate
                       @click="mobileMenu = false"
                       class="block rounded-control px-3 py-2 text-base font-semibold text-ink hover:bg-surface">
                        Profile
                    </a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                           wire:navigate
                           @click="mobileMenu = false"
                           class="block rounded-control px-3 py-2 text-base font-semibold text-ink hover:bg-surface">
                            Admin dashboard
                        </a>
                    @endif
                    <hr class="my-2 border-line">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                @click="mobileMenu = false"
                                class="block w-full rounded-control px-3 py-2 text-base font-semibold text-red-600 hover:bg-red-50 text-left">
                            Log out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       wire:navigate
                       @click="mobileMenu = false"
                       class="block rounded-control px-3 py-2 text-base font-semibold text-ink hover:bg-surface">
                        Log in
                    </a>
                    <a href="{{ route('register') }}"
                       wire:navigate
                       @click="mobileMenu = false"
                       class="block rounded-control px-3 py-2 text-base font-semibold text-brand-600 hover:bg-brand-50 text-left">
                        Register
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="min-h-[calc(100vh-4rem)]">
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

    <div x-data="{ message: @js(session('success')), show: @js((bool) session('success')), timer: null }"
         x-init="if (show) timer = setTimeout(() => show = false, 4000)"
         x-on:toast.window="message = $event.detail.message; show = true; clearTimeout(timer); timer = setTimeout(() => show = false, 4000)"
         x-show="show"
         x-cloak
         class="fixed bottom-5 left-1/2 z-50 flex max-w-[90vw] -translate-x-1/2 items-center gap-2 rounded-card bg-ink px-4 py-3 text-sm font-medium text-white shadow-float">
        <x-icon name="check" class="h-4 w-4 shrink-0 text-emerald-400" />
        <span x-text="message"></span>
    </div>

    @if (session('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 6000)"
             x-cloak
             class="fixed bottom-5 left-1/2 z-50 flex -translate-x-1/2 items-center gap-2 rounded-card bg-red-600 px-4 py-3 text-sm font-medium text-white shadow-float">
            <x-icon name="alert-circle" class="h-4 w-4" />
            {{ session('error') }}
        </div>
    @endif
</body>
</html>