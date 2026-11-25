<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $title ?? 'Dashboard') — NusaStay Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface font-sans text-ink antialiased">
    <div x-data="{ sidebar: false }" class="min-h-screen lg:flex">
        <div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-30 bg-ink/40 lg:hidden"></div>

        <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-40 flex w-[264px] flex-col border-r border-line bg-white transition-transform lg:static lg:translate-x-0">
            <div class="flex h-16 items-center gap-2 border-b border-line px-5">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-1.5 text-xl font-extrabold tracking-tight">
                    <span class="text-brand-600">nusa</span><span class="text-ink">stay</span>
                    <span class="ml-1 rounded bg-brand-50 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-brand-700">Admin</span>
                </a>
                <button @click="sidebar = false" class="ml-auto rounded p-1 text-muted lg:hidden" aria-label="Close sidebar"><x-icon name="x" class="h-5 w-5" /></button>
            </div>

            <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5 text-sm">
                <div>
                    <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-muted">Overview</p>
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-3 rounded-control px-3 py-2.5 font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-brand-50 text-brand-700' : 'text-ink hover:bg-surface' }}">
                        <x-icon name="grid" class="h-4 w-4" /> Dashboard
                    </a>
                    <a href="{{ route('admin.bookings.index') }}" wire:navigate class="flex items-center gap-3 rounded-control px-3 py-2.5 font-semibold {{ request()->routeIs('admin.bookings.*') ? 'bg-brand-50 text-brand-700' : 'text-ink hover:bg-surface' }}">
                        <x-icon name="calendar" class="h-4 w-4" /> Bookings
                    </a>
                    <a href="{{ route('admin.payments.index') }}" wire:navigate class="flex items-center gap-3 rounded-control px-3 py-2.5 font-semibold {{ request()->routeIs('admin.payments.*') ? 'bg-brand-50 text-brand-700' : 'text-ink hover:bg-surface' }}">
                        <x-icon name="credit-card" class="h-4 w-4" /> Payments
                    </a>
                </div>
                <div>
                    <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-muted">Inventory</p>
                    <a href="{{ route('admin.hotels.index') }}" wire:navigate class="flex items-center gap-3 rounded-control px-3 py-2.5 font-semibold {{ request()->routeIs('admin.hotels.*') ? 'bg-brand-50 text-brand-700' : 'text-ink hover:bg-surface' }}">
                        <x-icon name="hotel" class="h-4 w-4" /> Hotels
                    </a>
                    <a href="{{ route('admin.room-types.index') }}" wire:navigate class="flex items-center gap-3 rounded-control px-3 py-2.5 font-semibold {{ request()->routeIs('admin.room-types.*') ? 'bg-brand-50 text-brand-700' : 'text-ink hover:bg-surface' }}">
                        <x-icon name="bed" class="h-4 w-4" /> Room types
                    </a>
                    <a href="{{ route('admin.rooms.index') }}" wire:navigate class="flex items-center gap-3 rounded-control px-3 py-2.5 font-semibold {{ request()->routeIs('admin.rooms.*') ? 'bg-brand-50 text-brand-700' : 'text-ink hover:bg-surface' }}">
                        <x-icon name="tag" class="h-4 w-4" /> Rooms
                    </a>
                </div>
                <div>
                    <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-muted">People</p>
                    <a href="{{ route('admin.users.index') }}" wire:navigate class="flex items-center gap-3 rounded-control px-3 py-2.5 font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-brand-50 text-brand-700' : 'text-ink hover:bg-surface' }}">
                        <x-icon name="users" class="h-4 w-4" /> Users
                    </a>
                </div>
                <div class="border-t border-line pt-4">
                    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3 rounded-control px-3 py-2.5 font-semibold text-muted hover:bg-surface">
                        <x-icon name="arrow-right" class="h-4 w-4" /> View website
                    </a>
                </div>
            </nav>

            <div class="border-t border-line p-3">
                <div class="flex items-center gap-3 rounded-card bg-surface p-3">
                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="h-9 w-9 rounded-full object-cover">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-muted">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded p-1.5 text-muted transition-colors hover:bg-white hover:text-red-600" title="Log out">
                            <x-icon name="logout" class="h-4 w-4" />
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-line bg-white px-4 sm:px-6">
                <button @click="sidebar = true" class="rounded-control p-2 text-muted hover:bg-surface lg:hidden" aria-label="Open sidebar"><x-icon name="menu" class="h-5 w-5" /></button>
                <div>
                    <h1 class="text-lg font-bold leading-tight">@yield('title', $title ?? 'Dashboard')</h1>
                    <p class="text-xs text-muted">@yield('subtitle', $subtitle ?? now()->format('l, j F Y'))</p>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('home') }}" wire:navigate class="hidden rounded-control border border-line px-3 py-2 text-sm font-semibold text-muted hover:bg-surface sm:inline-flex">View site</a>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 rounded-full border border-line py-1 pl-1 pr-2.5 text-sm font-semibold hover:bg-surface">
                            <img src="{{ auth()->user()->avatar_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                            <x-icon name="chevron-down" class="h-4 w-4 text-muted" />
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false" class="absolute right-0 top-12 w-48 overflow-hidden rounded-card border border-line bg-white py-1.5 shadow-float">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm font-medium hover:bg-surface">Profile</a>
                            <a href="{{ route('home') }}" class="block px-4 py-2 text-sm font-medium hover:bg-surface">Customer site</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50">Log out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-[1440px] p-4 sm:p-6">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-cloak
             class="fixed bottom-5 left-1/2 z-50 flex -translate-x-1/2 items-center gap-2 rounded-card bg-ink px-4 py-3 text-sm font-medium text-white shadow-float">
            <x-icon name="check" class="h-4 w-4 text-emerald-400" />
            {{ session('success') }}
        </div>
    @endif
</body>
</html>
