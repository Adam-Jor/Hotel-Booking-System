<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Account' }} — NusaStay</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-ink antialiased">
    <div class="flex min-h-screen">
        <div class="relative hidden w-1/2 overflow-hidden bg-brand-900 lg:block">
            <img src="https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1600&q=70" alt="Coastal resort in Indonesia" class="absolute inset-0 h-full w-full object-cover opacity-70">
            <div class="absolute inset-0 bg-gradient-to-t from-brand-900 via-brand-900/30 to-transparent"></div>
            <div class="relative flex h-full flex-col justify-between p-10 text-white">
                <a href="{{ route('home') }}" class="flex items-center gap-1.5 text-xl font-extrabold tracking-tight">
                    nusastay
                    <svg class="h-5 w-5 text-accent" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.5 3.5 2.8 10.9c-.8.3-.8 1.5.1 1.7l6.4 1.8 1.8 6.4c.2.9 1.4.9 1.7.1L21.5 3.5Z"/></svg>
                </a>
                <div class="max-w-md">
                    <p class="text-3xl font-bold leading-tight">Rooms by the water, booked in a minute.</p>
                    <p class="mt-3 text-sm leading-relaxed text-white/75">Clear nightly prices, live availability and a booking you can manage yourself.</p>
                </div>
                <p class="text-xs text-white/60">NusaStay · Indonesia</p>
            </div>
        </div>

        <div class="flex w-full flex-col lg:w-1/2">
            <div class="flex h-16 items-center px-6 lg:px-12">
                <a href="{{ route('home') }}" class="flex items-center gap-1.5 text-xl font-extrabold tracking-tight lg:hidden">
                    <span class="text-brand-600">nusa</span><span class="text-ink">stay</span>
                </a>
                <a href="{{ route('hotels.index') }}" wire:navigate class="ml-auto text-sm font-semibold text-muted hover:text-ink">Browse hotels</a>
            </div>
            <div class="flex flex-1 items-center justify-center px-6 pb-16 lg:px-12">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
