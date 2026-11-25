<?php

namespace Tests\Feature;

use App\Exceptions\RoomNotAvailableException;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->customer = User::factory()->create(['role' => 'customer']);

        $hotel = Hotel::create([
            'name' => 'Test Beach Hotel',
            'slug' => 'test-beach-hotel',
            'city' => 'Bali',
            'address' => 'Jl. Pantai 1, Bali',
            'star_rating' => 4,
            'description' => 'A quiet test hotel.',
            'status' => 1,
        ]);

        $type = RoomType::create([
            'name' => 'Deluxe',
            'slug' => 'deluxe',
            'capacity' => 2,
            'description' => 'Sea view.',
        ]);

        $this->room = Room::create([
            'id_hotel' => $hotel->id,
            'id_room_type' => $type->id,
            'room_number' => '101',
            'slug' => 'test-beach-hotel-deluxe-101',
            'price' => 800000,
            'status' => 'available',
        ]);
    }

    public function test_public_pages_render(): void
    {
        $this->get('/')->assertOk();
        $this->get('/hotels')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
        $this->get('/hotels/test-beach-hotel')->assertOk();
    }

    public function test_admin_pages_render(): void
    {
        $this->actingAs($this->admin);

        foreach ([
            '/admin/dashboard',
            '/admin/hotels',
            '/admin/room-types',
            '/admin/rooms',
            '/admin/bookings',
            '/admin/payments',
            '/admin/users',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_booking_holds_room_and_blocks_double_booking(): void
    {
        $service = app(BookingService::class);
        $checkIn = Carbon::today()->addDays(2)->toDateString();
        $checkOut = Carbon::today()->addDays(4)->toDateString();

        $booking = $service->createPending([
            'id_room' => $this->room->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guest_count' => 2,
            'customer_name' => 'Rina',
        ], $this->customer);

        $this->assertSame('pending', $booking->status);
        $this->assertSame(2, $booking->total_nights);
        $this->assertSame(1600000, (int) $booking->total_price);
        $this->assertTrue($booking->payments()->exists());

        $this->expectException(RoomNotAvailableException::class);

        $service->createPending([
            'id_room' => $this->room->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guest_count' => 1,
            'customer_name' => 'Other guest',
        ], $this->customer);
    }

    public function test_approved_payment_marks_booking_paid(): void
    {
        $booking = app(BookingService::class)->createPending([
            'id_room' => $this->room->id,
            'check_in' => Carbon::today()->addDays(5)->toDateString(),
            'check_out' => Carbon::today()->addDays(6)->toDateString(),
            'guest_count' => 1,
            'customer_name' => 'Rina',
        ], $this->customer);

        $payment = $booking->payments()->firstOrFail();
        app(PaymentService::class)->approve($payment, $this->admin);

        $this->assertSame('paid', $payment->fresh()->status);
        $this->assertSame('paid', $booking->fresh()->status);
    }

    public function test_customer_can_download_own_invoice_only(): void
    {
        $booking = app(BookingService::class)->createPending([
            'id_room' => $this->room->id,
            'check_in' => Carbon::today()->addDays(7)->toDateString(),
            'check_out' => Carbon::today()->addDays(8)->toDateString(),
            'guest_count' => 1,
            'customer_name' => 'Rina',
        ], $this->customer);

        $this->actingAs($this->customer)
            ->get(route('bookings.invoice', $booking))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $stranger = User::factory()->create();

        $this->actingAs($stranger)
            ->get(route('bookings.invoice', $booking))
            ->assertForbidden();
    }

    public function test_pending_booking_can_be_cancelled_by_customer(): void
    {
        $service = app(BookingService::class);

        $booking = $service->createPending([
            'id_room' => $this->room->id,
            'check_in' => Carbon::today()->addDays(9)->toDateString(),
            'check_out' => Carbon::today()->addDays(10)->toDateString(),
            'guest_count' => 1,
            'customer_name' => 'Rina',
        ], $this->customer);

        $service->cancel($booking, 'customer');

        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertSame('failed', $booking->payments()->first()->status);
    }
}
