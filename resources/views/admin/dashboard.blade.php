@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Overview for '.now()->format('F Y'))

@section('content')
    @php
        $statusBadge = fn ($status) => match ($status) {
            'paid' => 'bg-emerald-50 text-emerald-700',
            'pending' => 'bg-amber-50 text-amber-700',
            'completed' => 'bg-brand-50 text-brand-700',
            'cancelled' => 'bg-red-50 text-red-700',
            default => 'bg-surface text-muted',
        };
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-card border border-line bg-white p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-muted">Total bookings</p>
                <x-icon name="calendar" class="h-4 w-4 text-muted" />
            </div>
            <p class="mt-2 text-2xl font-extrabold tabular-nums">{{ number_format($stats['bookings']) }}</p>
            <p class="mt-1 text-xs text-muted">{{ $statusCounts['pending'] ?? 0 }} pending · {{ $statusCounts['paid'] ?? 0 }} paid</p>
        </div>
        <div class="rounded-card border border-line bg-white p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-muted">Rooms available</p>
                <x-icon name="bed" class="h-4 w-4 text-muted" />
            </div>
            <p class="mt-2 text-2xl font-extrabold tabular-nums">{{ number_format($stats['rooms']) }}</p>
            <p class="mt-1 text-xs text-muted">across {{ $stats['hotels'] }} hotels</p>
        </div>
        <div class="rounded-card border border-line bg-white p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-muted">Customers</p>
                <x-icon name="users" class="h-4 w-4 text-muted" />
            </div>
            <p class="mt-2 text-2xl font-extrabold tabular-nums">{{ number_format($stats['customers']) }}</p>
            <p class="mt-1 text-xs text-muted">registered accounts</p>
        </div>
        <div class="rounded-card border border-brand-200 bg-brand-50 p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-brand-700">Revenue</p>
                <x-icon name="credit-card" class="h-4 w-4 text-brand-700" />
            </div>
            <p class="mt-2 text-2xl font-extrabold tabular-nums text-brand-900">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-brand-700/80">verified payments</p>
        </div>
    </div>

    <div class="mt-5 grid gap-5 xl:grid-cols-[1.5fr_1fr]">
        <div class="rounded-card border border-line bg-white p-5" x-data="{ open: false, mode: @js($mode) }">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold">Revenue {{ $mode === 'month' ? 'by day' : 'by month' }}</h2>
                    <p class="mt-0.5 text-xs text-muted">{{ $periodLabel }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.revenue.export', ['mode' => $mode, 'months' => $months, 'month' => $month, 'year' => $year]) }}"
                       class="inline-flex h-9 items-center gap-1.5 rounded-control border border-line px-3 text-xs font-semibold text-muted transition-colors hover:border-brand-300 hover:text-brand-700">
                        <x-icon name="download" class="h-3.5 w-3.5" /> Export CSV
                    </a>

                    <div class="relative">
                        <button type="button" @click="open = !open"
                                class="inline-flex h-9 items-center gap-1.5 rounded-control border border-line px-3 text-xs font-semibold text-ink transition-colors hover:border-brand-300"
                                :aria-expanded="open.toString()">
                            <x-icon name="sliders-horizontal" class="h-3.5 w-3.5" /> Filter
                        </button>

                        <div x-show="open"
                             x-cloak
                             @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="absolute right-0 z-40 mt-2 w-80 rounded-card border border-line bg-white p-4 shadow-float">
                            <form method="GET" class="space-y-3">
                                <input type="hidden" name="mode" :value="mode">

                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" @click="mode = 'range'"
                                            :class="mode === 'range' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-line text-muted hover:border-brand-300'"
                                            class="rounded-control border px-3 py-2 text-xs font-semibold transition-colors">
                                        Rentang bulan
                                    </button>
                                    <button type="button" @click="mode = 'month'"
                                            :class="mode === 'month' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-line text-muted hover:border-brand-300'"
                                            class="rounded-control border px-3 py-2 text-xs font-semibold transition-colors">
                                        Nama bulan
                                    </button>
                                </div>

                                <div x-show="mode === 'range'" class="space-y-1.5">
                                    <label for="filter-months" class="label">Rentang (mulai Januari)</label>
                                    <select id="filter-months" name="months" class="field">
                                        @foreach ([3 => '3 bulan (Jan – Mar)', 6 => '6 bulan (Jan – Jun)', 9 => '9 bulan (Jan – Sep)', 12 => '12 bulan (Jan – Dec)'] as $value => $label)
                                            <option value="{{ $value }}" @selected($months === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div x-show="mode === 'month'" class="space-y-1.5">
                                    <label for="filter-month" class="label">Bulan</label>
                                    <select id="filter-month" name="month" class="field">
                                        @foreach (range(1, 12) as $value)
                                            <option value="{{ $value }}" @selected($month === $value)>
                                                {{ \Illuminate\Support\Carbon::create($year, $value, 1)->format('F') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="space-y-1.5">
                                    <label for="filter-year" class="label">Tahun</label>
                                    <select id="filter-year" name="year" class="field">
                                        @foreach ($years as $option)
                                            <option value="{{ $option }}" @selected($year === $option)>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex justify-end gap-2 border-t border-line pt-3">
                                    <button type="button" @click="open = false" class="btn-ghost border border-line">Cancel</button>
                                    <button type="submit" class="btn-primary">Apply Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <span class="badge bg-brand-50 text-brand-700">Rp {{ number_format($revenue->sum('value'), 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="relative mt-6 h-64">
                <canvas id="revenueChart"
                        data-type="{{ $mode === 'month' ? 'line' : 'bar' }}"
                        data-labels="{{ $revenue->pluck('label')->toJson() }}"
                        data-values="{{ $revenue->pluck('value')->toJson() }}"
                        aria-label="Revenue chart" role="img"></canvas>
            </div>
        </div>

        <div class="rounded-card border border-line bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold">Needs verification</h2>
                <a href="{{ route('admin.payments.index') }}" class="text-xs font-semibold text-brand-600">All payments</a>
            </div>
            <div class="mt-3 space-y-3">
                @forelse ($pendingPayments as $payment)
                    <div class="flex items-center gap-3 rounded-control border border-line p-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-700"><x-icon name="credit-card" class="h-4 w-4" /></div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold">{{ $payment->booking?->booking_code }} · {{ $payment->booking?->customer_name }}</p>
                            <p class="text-xs text-muted">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }} · {{ $payment->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('admin.payments.index') }}" class="text-xs font-semibold text-brand-600">Review</a>
                    </div>
                @empty
                    <p class="rounded-control bg-surface px-3 py-6 text-center text-sm text-muted">No payment waiting for verification.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-5 grid gap-5 xl:grid-cols-[1.5fr_1fr]">
        <div class="rounded-card border border-line bg-white">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <h2 class="text-base font-bold">Recent bookings</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-xs font-semibold text-brand-600">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface">
                        <tr>
                            <th class="table-th">Code</th>
                            <th class="table-th">Customer</th>
                            <th class="table-th">Hotel & room</th>
                            <th class="table-th">Check-in</th>
                            <th class="table-th">Total</th>
                            <th class="table-th">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($recent as $booking)
                            <tr class="hover:bg-surface/60">
                                <td class="table-td font-semibold">{{ $booking->booking_code }}</td>
                                <td class="table-td">{{ $booking->customer_name }}</td>
                                <td class="table-td">
                                    <p class="font-medium">{{ $booking->hotel_name }}</p>
                                    <p class="text-xs text-muted">{{ $booking->room?->roomType?->name }} · {{ $booking->room_number }}</p>
                                </td>
                                <td class="table-td whitespace-nowrap">{{ $booking->check_in_date->format('j M Y') }}</td>
                                <td class="table-td font-semibold whitespace-nowrap">Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</td>
                                <td class="table-td"><span class="badge {{ $statusBadge($booking->status) }}">{{ $booking->status_label }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-muted">No bookings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <livewire:admin.quick-booking-form />
    </div>
@endsection
