<?php

namespace App\Http\Controllers\Front;

use App\Models\Room;

class CheckoutController
{
    public function index()
    {
        $room = Room::with('hotel', 'roomType')
            ->where('archived', 0)
            ->findOrFail(request('room_id'));

        return view('front.checkout', compact('room'));
    }
}
