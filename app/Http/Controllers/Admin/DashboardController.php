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

        $months = (int) request('months', 6);
        if (! in_array($months, [1, 3, 6, 9, 12], true)) {
            $months = 6;
        }

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

        $end = $year === $currentYear
            ? now()->startOfMonth()
            : Carbon::create($year, 12, 1)->startOfMonth();

        $revenueByMonth = collect(range($months - 1, 0))->map(function ($i) use ($end) {
            $month = $end->copy()->subMonths($i);
            $total = Payment::where('status', 'paid')
                ->whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->sum('amount');

            return [
                'label' => $month->format('M Y'),
                'value' => (float) $total,
            ];
        });

        $statusCounts = Booking::where('archived', 0)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.dashboard', compact('stats', 'recent', 'pendingPayments', 'revenueByMonth', 'months', 'year', 'years', 'statusCounts'));
    }
}
