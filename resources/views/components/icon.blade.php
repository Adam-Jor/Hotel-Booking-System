@props(['name' => 'search', 'class' => 'h-5 w-5'])

<svg {{ $attributes->merge(['class' => $class]) }} xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
    @switch($name)
        @case('search')
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
            @break
        @case('map-pin')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.25 7-11a7 7 0 1 0-14 0c0 5.75 7 11 7 11Z" />
            <circle cx="12" cy="10" r="2.5" />
            @break
        @case('calendar')
            <rect x="3.5" y="5" width="17" height="16" rx="2.5" />
            <path stroke-linecap="round" d="M8 3v4M16 3v4M3.5 10h17" />
            @break
        @case('users')
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 19v-1.5a4 4 0 0 0-4-4H6.5a4 4 0 0 0-4 4V19M9.25 9.5a3.25 3.25 0 1 0 0-6.5 3.25 3.25 0 0 0 0 6.5ZM21.5 19v-1.5a4 4 0 0 0-3-3.87M16.25 3.13a3.25 3.25 0 0 1 0 6.3" />
            @break
        @case('star')
            <path stroke-linejoin="round" d="m12 3 2.7 5.47 6.03.88-4.36 4.25 1.03 6.01L12 16.77l-5.4 2.84 1.03-6.01L3.27 9.35l6.03-.88L12 3Z" />
            @break
        @case('wifi')
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 8.5a15 15 0 0 1 19 0M5.5 12a10.5 10.5 0 0 1 13 0M8.5 15.5a6 6 0 0 1 7 0" />
            <circle cx="12" cy="19" r="1" fill="currentColor" />
            @break
        @case('waves')
            <path stroke-linecap="round" stroke-linejoin="round" d="M2 8c2 0 2-1.5 4-1.5S8 8 10 8s2-1.5 4-1.5S16 8 18 8s2-1.5 4-1.5M2 13c2 0 2-1.5 4-1.5S8 13 10 13s2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5M2 18c2 0 2-1.5 4-1.5S8 18 10 18s2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5" />
            @break
        @case('coffee')
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h12v6a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4V8ZM16 9h1.5a2.5 2.5 0 0 1 0 5H16M6 3v2M10 3v2M14 3v2M3 21h14" />
            @break
        @case('car')
            <path stroke-linecap="round" stroke-linejoin="round" d="m5 11 1.6-4.2A2 2 0 0 1 8.5 5.5h7a2 2 0 0 1 1.9 1.3L19 11M3 11h18v6a1 1 0 0 1-1 1h-1.5a1 1 0 0 1-1-1v-1H6.5v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-6Z" />
            <circle cx="7" cy="14" r="1" fill="currentColor" />
            <circle cx="17" cy="14" r="1" fill="currentColor" />
            @break
        @case('wind')
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8h10a3 3 0 1 0-3-3M3 12h15a3 3 0 1 1-3 3M3 16h7" />
            @break
        @case('flower')
            <circle cx="12" cy="12" r="2.5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.5V6M12 18v-3.5M9.5 12H6M18 12h-3.5M10.2 10.2 7.8 7.8M16.2 16.2l-2.4-2.4M13.8 10.2l2.4-2.4M7.8 16.2l2.4-2.4" />
            @break
        @case('dumbbell')
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.5 6.5v11M3.5 9v5M17.5 6.5v11M20.5 9v5M6.5 12h11" />
            @break
        @case('utensils')
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3v8M4.5 3v4a2.5 2.5 0 0 0 5 0V3M7 11v10M16 3c-1.7 1.5-2.5 3.5-2.5 6.5 0 1.5.8 2.5 2.5 2.5V21M16 3v9" />
            @break
        @case('chevron-down')
            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
            @break
        @case('menu')
            <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
            @break
        @case('x')
            <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18" />
            @break
        @case('check')
            <path stroke-linecap="round" stroke-linejoin="round" d="m5 12.5 4.5 4.5L19 7" />
            @break
        @case('clock')
            <circle cx="12" cy="12" r="8.5" />
            <path stroke-linecap="round" d="M12 7.5V12l3 2" />
            @break
        @case('download')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4.5-4.5M12 15l-4.5-4.5M4 17v2.5A1.5 1.5 0 0 0 5.5 21h13a1.5 1.5 0 0 0 1.5-1.5V17" />
            @break
        @case('message-circle')
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.5a8.5 8.5 0 0 1-12.3 7.6L3 21l1.9-5.7A8.5 8.5 0 1 1 21 11.5Z" />
            @break
        @case('hotel')
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V5.5A1.5 1.5 0 0 1 4.5 4h9A1.5 1.5 0 0 1 15 5.5V21M15 10h4.5A1.5 1.5 0 0 1 21 11.5V21M2 21h20M6.5 8h2M6.5 12h2M6.5 16h2M11 8h1M11 12h1M11 16h1M18 14h.5M18 18h.5" />
            @break
        @case('bed')
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 19V7M3 15h18M21 19v-4M3 11h6a2 2 0 0 1 2 2v2M11 15h10v-2a2 2 0 0 0-2-2h-4" />
            @break
        @case('arrow-right')
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16m0 0-6-6m6 6-6 6" />
            @break
        @case('credit-card')
            <rect x="2.5" y="5" width="19" height="14" rx="2.5" />
            <path stroke-linecap="round" d="M2.5 9.5h19M6 15h4" />
            @break
        @case('shield')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4.5 6v5.5c0 4.5 3.2 8.2 7.5 9.5 4.3-1.3 7.5-5 7.5-9.5V6L12 3Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4" />
            @break
        @case('pencil')
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20h4l10.5-10.5a2.1 2.1 0 0 0-3-3L5 17v3ZM13.5 6.5l3 3" />
            @break
        @case('trash')
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V4.5h6V7M6.5 7l.8 12A2 2 0 0 0 9.3 21h5.4a2 2 0 0 0 2-1.9l.8-12M10 11v6M14 11v6" />
            @break
        @case('plus')
            <path stroke-linecap="round" d="M12 5v14M5 12h14" />
            @break
        @case('logout')
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 4h3.5A1.5 1.5 0 0 1 20 5.5v13a1.5 1.5 0 0 1-1.5 1.5H15M10 16l4-4-4-4M14 12H3" />
            @break
        @case('user')
            <circle cx="12" cy="8" r="3.5" />
            <path stroke-linecap="round" d="M5 20a7 7 0 0 1 14 0" />
            @break
        @case('grid')
            <rect x="3.5" y="3.5" width="7" height="7" rx="1.5" />
            <rect x="13.5" y="3.5" width="7" height="7" rx="1.5" />
            <rect x="3.5" y="13.5" width="7" height="7" rx="1.5" />
            <rect x="13.5" y="13.5" width="7" height="7" rx="1.5" />
            @break
        @case('tag')
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13 13 20a2 2 0 0 1-2.8 0L3 12.8V3h9.8L20 10.2a2 2 0 0 1 0 2.8Z" />
            <circle cx="7.5" cy="7.5" r="1.5" />
            @break
        @case('chart')
            <path stroke-linecap="round" d="M4 20V10M10 20V4M16 20v-7M22 20H2" />
            @break
        @default
            <circle cx="12" cy="12" r="8" />
    @endswitch
</svg>
