<?php

namespace App\Http\Controllers\Front;

use App\Models\Hotel;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController
{
    public function index(Request $request, AvailabilityService $availability)
    {
        $cities = Cache::remember('home.cities', 300, function () {
            return Hotel::active()
                ->selectRaw('city, count(*) as hotels')
                ->groupBy('city')
                ->orderByDesc('hotels')
                ->limit(8)
                ->get();
        });

        $hotels = Hotel::active()
            ->with('facilities')
            ->withMin(['rooms' => fn ($q) => $q->where('archived', 0)->where('status', 'available')], 'price')
            ->withAvg('reviews', 'rating')
            ->withCount(['reviews', 'rooms'])
            ->orderByDesc('star_rating')
            ->limit(6)
            ->get();

        $stats = [
            'hotels' => Hotel::active()->count(),
            'cities' => Hotel::active()->distinct('city')->count('city'),
        ];

        return view('front.home', compact('cities', 'hotels', 'stats'));
    }
}
