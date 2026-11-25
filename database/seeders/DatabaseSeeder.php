<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Hotel',
            'username' => 'admin',
            'email' => 'admin@hotel.test',
            'password' => Hash::make('password'),
            'phone' => '6281234567890',
            'role' => 'admin',
            'status' => 1,
        ]);

        $customer = User::create([
            'name' => 'Rina Wijaya',
            'username' => 'rina',
            'email' => 'customer@hotel.test',
            'password' => Hash::make('password'),
            'phone' => '6281298765432',
            'role' => 'customer',
            'status' => 1,
        ]);

        $guests = collect(range(1, 6))->map(fn ($i) => User::create([
            'name' => fake()->name(),
            'username' => 'guest'.$i,
            'email' => "guest{$i}@hotel.test",
            'password' => Hash::make('password'),
            'phone' => '62812'.fake()->numerify('#######'),
            'role' => 'customer',
            'status' => 1,
        ]));

        $facilities = collect([
            ['name' => 'Free WiFi', 'icon' => 'wifi'],
            ['name' => 'Swimming Pool', 'icon' => 'waves'],
            ['name' => 'Breakfast', 'icon' => 'coffee'],
            ['name' => 'Parking', 'icon' => 'car'],
            ['name' => 'Air Conditioning', 'icon' => 'wind'],
            ['name' => 'Spa', 'icon' => 'flower'],
            ['name' => 'Gym', 'icon' => 'dumbbell'],
            ['name' => 'Restaurant', 'icon' => 'utensils'],
        ])->map(fn ($f) => Facility::create($f));

        $types = collect([
            ['name' => 'Standard', 'capacity' => 2, 'description' => 'Comfortable room with a queen bed, air conditioning and city view.'],
            ['name' => 'Superior', 'capacity' => 3, 'description' => 'Extra space with a king bed, work desk and garden view.'],
            ['name' => 'Deluxe', 'capacity' => 3, 'description' => 'Premium room with balcony, seating area and pool access.'],
            ['name' => 'Suite', 'capacity' => 4, 'description' => 'Two-room suite with living area, bathtub and panoramic view.'],
        ])->map(fn ($t) => RoomType::create($t + ['slug' => Str::slug($t['name'])]));

        $hotels = [
            ['Grand Aston Bali Beach Resort', 'Bali', 5, 1850000, 'photo-1566073771259-6a8506099945', 'Beachfront resort on the calm sands of Nusa Dua with a lagoon pool and sunset bar.'],
            ['The Kayon Jungle Resort', 'Bali', 5, 2450000, 'photo-1582719508461-905c673771fd', 'Adults-only jungle retreat above Ubud with infinity pools and rice terrace views.'],
            ['Hotel Indonesia Kempinski', 'Jakarta', 5, 1650000, 'photo-1571896349842-33c89424de2d', 'Landmark hotel on Jalan Thamrin, steps from shopping and the city business district.'],
            ['Morrissey Hotel Jakarta', 'Jakarta', 4, 850000, 'photo-1520250497591-112f2f40a3f4', 'Quiet boutique hotel in Menteng with a rooftop pool and all-day dining.'],
            ['Padma Hotel Bandung', 'Bandung', 5, 1400000, 'photo-1551882547-ff40c63fe5fa', 'Hillside hotel surrounded by greenery with a heated pool and family activities.'],
            ['The Trans Luxury Hotel', 'Bandung', 5, 1250000, 'photo-1542314831-068cd1dbfeeb', 'Modern hotel connected to a mall, with a rooftop lounge and spacious rooms.'],
            ['Hyatt Regency Yogyakarta', 'Yogyakarta', 5, 1100000, 'photo-1445019980597-93fa8acb246c', 'Resort-style hotel with a golf course, lagoon pool and Javanese hospitality.'],
            ['Hotel Tentrem Yogyakarta', 'Yogyakarta', 5, 1350000, 'photo-1564501049412-61c2a3083791', 'Five-star heritage hotel known for its breakfast and central location.'],
            ['JW Marriott Surabaya', 'Surabaya', 5, 1200000, 'photo-1566073771259-6a8506099945', 'Business hotel in the city centre with an outdoor pool and executive lounge.'],
            ['Aryaduta Medan', 'Medan', 4, 650000, 'photo-1582719508461-905c673771fd', 'Comfortable city hotel close to culinary spots and the airport road.'],
            ['Katamaran Hotel & Resort', 'Lombok', 4, 950000, 'photo-1571896349842-33c89424de2d', 'Beachfront resort in Senggigi with ocean-view rooms and a dive centre.'],
            ['Loccal Collection Hotel', 'Labuan Bajo', 4, 1050000, 'photo-1520250497591-112f2f40a3f4', 'Hillside hotel overlooking the harbour, a short walk from the main street.'],
        ];

        foreach ($hotels as $i => [$name, $city, $stars, $basePrice, $photo, $desc]) {
            $hotel = Hotel::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'city' => $city,
                'address' => fake()->streetAddress().', '.$city,
                'latitude' => fake()->latitude(-8, -6),
                'longitude' => fake()->longitude(106, 116),
                'star_rating' => $stars,
                'description' => $desc,
                'img_url' => "https://images.unsplash.com/{$photo}?auto=format&fit=crop&w=1200&q=70",
                'status' => 1,
            ]);

            $hotel->facilities()->attach(
                $facilities->random(rand(4, 6))->pluck('id')->all()
            );

            foreach ([$photo, 'photo-1611892440504-42a792e24d32', 'photo-1590490360182-c33d57733427'] as $order => $img) {
                HotelImage::create([
                    'id_hotel' => $hotel->id,
                    'img_url' => "https://images.unsplash.com/{$img}?auto=format&fit=crop&w=1200&q=70",
                    'sort_order' => $order,
                ]);
            }

            foreach ($types as $t => $type) {
                $price = (int) round(($basePrice + $t * 350000) / 1000) * 1000;
                foreach ([1, 2] as $n) {
                    Room::create([
                        'id_hotel' => $hotel->id,
                        'id_room_type' => $type->id,
                        'room_number' => (100 + $t * 10 + $n).'',
                        'slug' => Str::slug($hotel->slug.'-'.$type->name.'-'.$n),
                        'price' => $price,
                        'description' => $type->description,
                        'img_url' => "https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1200&q=70",
                        'status' => 'available',
                    ]);
                }
            }

            $room = $hotel->rooms()->inRandomOrder()->first();
            $checkIn = Carbon::today()->subDays(rand(5, 30));
            $nights = rand(1, 3);
            $status = ['paid', 'completed', 'cancelled'][rand(0, 2)];
            $guest = $guests->random();

            $booking = Booking::create([
                'booking_code' => 'BKG-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'id_user' => $guest->id,
                'id_room' => $room->id,
                'customer_name' => $guest->name,
                'guest_count' => rand(1, 2),
                'check_in_date' => $checkIn,
                'check_out_date' => $checkIn->copy()->addDays($nights),
                'total_nights' => $nights,
                'price_per_night' => $room->price,
                'total_price' => $room->price * $nights,
                'status' => $status,
                'notes' => null,
                'expires_at' => $status === 'pending' ? now()->addMinutes(60) : null,
                'cancelled_at' => $status === 'cancelled' ? now()->subDays(2) : null,
                'cancel_reason' => $status === 'cancelled' ? 'customer' : null,
            ]);

            if (in_array($status, ['paid', 'completed'])) {
                Payment::create([
                    'id_booking' => $booking->id,
                    'payment_method' => 'bank_transfer',
                    'amount' => $booking->total_price,
                    'payment_date' => now()->subDays(3),
                    'reference_code' => 'VA-'.strtoupper(Str::random(10)),
                    'verified_by' => $admin->id,
                    'verified_at' => now()->subDays(3),
                    'status' => 'paid',
                ]);

                if ($status === 'completed') {
                    Review::create([
                        'id_hotel' => $hotel->id,
                        'id_user' => $guest->id,
                        'id_booking' => $booking->id,
                        'rating' => rand(4, 5),
                        'comment' => 'Great stay, friendly staff and a very comfortable room.',
                    ]);
                }
            }

            // one pending booking on the first hotels
            if ($i < 3) {
                $pendingGuest = $i === 0 ? $customer : $guests->random();
                $ci = Carbon::today()->addDays(rand(3, 10));
                $pRoom = $hotel->rooms()->inRandomOrder()->first();
                $p = Booking::create([
                    'booking_code' => 'BKG-'.str_pad((string) (100 + $i), 3, '0', STR_PAD_LEFT),
                    'id_user' => $pendingGuest->id,
                    'id_room' => $pRoom->id,
                    'customer_name' => $pendingGuest->name,
                    'guest_count' => 2,
                    'check_in_date' => $ci,
                    'check_out_date' => $ci->copy()->addDays(2),
                    'total_nights' => 2,
                    'price_per_night' => $pRoom->price,
                    'total_price' => $pRoom->price * 2,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(60),
                ]);
                Payment::create([
                    'id_booking' => $p->id,
                    'payment_method' => 'bank_transfer',
                    'amount' => $p->total_price,
                    'reference_code' => 'VA-'.strtoupper(Str::random(10)),
                    'status' => 'pending',
                ]);
            }
        }
    }
}
