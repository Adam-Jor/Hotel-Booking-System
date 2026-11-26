<?php

use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\FrontHotelController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\MyBookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/hotels', [FrontHotelController::class, 'index'])->name('hotels.index');
Route::get('/hotels/{hotel:slug}', [FrontHotelController::class, 'show'])->name('hotels.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('my-bookings'))->name('dashboard');

    Route::post('/logout', \App\Livewire\Actions\Logout::class)->name('logout');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::get('/my-bookings', [MyBookingController::class, 'index'])->name('my-bookings');
    Route::get('/bookings/{booking}/invoice', [BookingController::class, 'invoice'])->name('bookings.invoice');

    Route::view('profile', 'profile')->name('profile');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/revenue/export', [DashboardController::class, 'exportRevenue'])->name('revenue.export');

    Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
    Route::get('/room-types', [RoomTypeController::class, 'index'])->name('room-types.index');
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/{booking}/whatsapp', [BookingController::class, 'resendWhatsapp'])->name('bookings.whatsapp');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
});

require __DIR__.'/auth.php';
