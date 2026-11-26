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
        <div class="rounded-card border border-line bg-white p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold">Revenue by month</h2>
                    <p class="mt-0.5 text-xs text-muted">Verified payments, last {{ $months }} {{ $months === 1 ? 'month' : 'months' }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <form method="GET" class="flex items-center gap-2">
                        <select name="months" onchange="this.form.submit()" aria-label="Month range"
                                class="field h-9 w-32 py-1 text-xs">
                            @foreach ([1 => '1 month', 3 => '3 months', 6 => '6 months', 9 => '9 months', 12 => '12 months'] as $value => $label)
                                <option value="{{ $value }}" @selected($months === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="year" onchange="this.form.submit()" aria-label="Year"
                                class="field h-9 w-24 py-1 text-xs">
                            @foreach ($years as $option)
                                <option value="{{ $option }}" @selected($year === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </form>
                    <span class="badge bg-brand-50 text-brand-700">Rp {{ number_format($revenueByMonth->sum('value'), 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="relative mt-6 h-64">
                <canvas id="revenueChart"
                        data-labels="{{ $revenueByMonth->pluck('label')->toJson() }}"
                        data-values="{{ $revenueByMonth->pluck('value')->toJson() }}"
                        aria-label="Revenue by month chart" role="img"></canvas>
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
