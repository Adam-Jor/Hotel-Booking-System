<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController
{
    public function index()
    {
        $stats = Cache::remember('admin.dashboard.stats', 300, function () {
            return [
                'bookings' => Booking::where('archived', 0)->count(),
                'rooms' => Room::where('archived', 0)->where('status', 'available')->count(),
                'hotels' => Hotel::where('archived', 0)->count(),
                'revenue' => (float) Payment::where('status', 'paid')->sum('amount'),
                'customers' => User::where('role', 'customer')->where('archived', 0)->count(),
                'needs_verification' => Payment::where('status', 'pending')->whereNotNull('proof_url')->count(),
            ];
        });

        $recent = Booking::query()
            ->select('tbl_bookings.*', 'tbl_rooms.room_number', 'tbl_hotels.name as hotel_name')
            ->join('tbl_rooms', 'tbl_rooms.id', '=', 'tbl_bookings.id_room')
            ->join('tbl_hotels', 'tbl_hotels.id', '=', 'tbl_rooms.id_hotel')
            ->with('room.roomType')
            ->where('tbl_bookings.archived', 0)
            ->latest('tbl_bookings.created_at')
            ->limit(8)
            ->get();

        $pendingPayments = Payment::with('booking.user')
            ->where('status', 'pending')
            ->whereNotNull('proof_url')
            ->latest()
            ->limit(5)
            ->get();

        $statusCounts = Booking::where('archived', 0)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.dashboard', [
            ...$this->revenueData(),
            'stats' => $stats,
            'recent' => $recent,
            'pendingPayments' => $pendingPayments,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function exportRevenue()
    {
        $data = $this->revenueData();
        $mode = $data['mode'];
        $filename = $mode === 'month'
            ? 'revenue-'.$data['year'].'-'.str_pad((string) $data['month'], 2, '0', STR_PAD_LEFT).'.csv'
            : 'revenue-'.$data['year'].'-first-'.$data['months'].'-months.csv';

        return response()->streamDownload(function () use ($data, $mode) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [$mode === 'month' ? 'Day' : 'Month', 'Revenue (IDR)']);
            foreach ($data['revenue'] as $row) {
                fputcsv($out, [$row['label'], $row['value']]);
            }
            fputcsv($out, ['Total', $data['revenue']->sum('value')]);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Revenue for the chart and CSV export, driven by the same filter.
     *
     * range: Jan..N months of the selected year (N = 3, 6, 9, 12)
     * month: every day of the selected month of the selected year
     */
    private function revenueData(): array
    {
        $currentYear = (int) now()->year;

        $range = Payment::where('status', 'paid')
            ->whereNotNull('payment_date')
            ->selectRaw('MIN(payment_date) as first_paid, MAX(payment_date) as last_paid')
            ->first();

        $firstYear = $range?->first_paid ? (int) Carbon::parse($range->first_paid)->year : $currentYear;
        $lastYear = max($currentYear, $range?->last_paid ? (int) Carbon::parse($range->last_paid)->year : $currentYear);
        $years = collect(range($lastYear, $firstYear));

        $year = (int) request('year', $currentYear);
        if (! $years->contains($year)) {
            $year = $currentYear;
        }

        $mode = request('mode') === 'month' ? 'month' : 'range';

        $months = (int) request('months', 6);
        if (! in_array($months, [3, 6, 9, 12], true)) {
            $months = 6;
        }

        $month = (int) request('month', now()->month);
        if ($month < 1 || $month > 12) {
            $month = (int) now()->month;
        }

        if ($mode === 'month') {
            $start = Carbon::create($year, $month, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();

            $totals = Payment::where('status', 'paid')
                ->whereBetween('payment_date', [$start, $end])
                ->get(['payment_date', 'amount'])
                ->groupBy(fn (Payment $payment) => (int) $payment->payment_date->format('j'))
                ->map(fn ($rows) => (float) $rows->sum('amount'));

            $revenue = collect(range(1, $start->daysInMonth))->map(fn ($day) => [
                'label' => (string) $day,
                'value' => $totals->get($day, 0.0),
            ]);

            $periodLabel = $start->format('F Y').' · daily revenue';
        } else {
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end = $start->copy()->addMonths($months - 1)->endOfMonth();

            $totals = Payment::where('status', 'paid')
                ->whereBetween('payment_date', [$start, $end])
                ->get(['payment_date', 'amount'])
                ->groupBy(fn (Payment $payment) => (int) $payment->payment_date->format('n'))
                ->map(fn ($rows) => (float) $rows->sum('amount'));

            $revenue = collect(range(1, $months))->map(fn ($index) => [
                'label' => Carbon::create($year, $index, 1)->format('M'),
                'value' => $totals->get($index, 0.0),
            ]);

            $periodLabel = 'January – '.Carbon::create($year, $months, 1)->format('F Y');
        }

        return [
            'mode' => $mode,
            'months' => $months,
            'month' => $month,
            'year' => $year,
            'years' => $years,
            'revenue' => $revenue,
            'periodLabel' => $periodLabel,
        ];
    }
}
