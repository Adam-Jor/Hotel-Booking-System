<?php

namespace App\Http\Controllers\Front;

use App\Models\Hotel;

class FrontHotelController
{
    public function index()
    {
        return view('front.search');
    }

    public function show(Hotel $hotel)
    {
        abort_unless($hotel->status === 1 && ! $hotel->archived, 404);

        $hotel->load([
            'images',
            'facilities',
            'reviews' => fn ($q) => $q->latest()->limit(6),
            'reviews.user',
            'rooms' => fn ($q) => $q->where('archived', 0)->where('status', 'available')->with('roomType'),
        ]);

        $rating = round((float) ($hotel->reviews->avg('rating') ?: 0), 1);

        return view('front.hotel-detail', compact('hotel', 'rating'));
    }
}
