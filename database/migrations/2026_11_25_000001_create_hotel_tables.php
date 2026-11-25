<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('city')->index();
            $table->text('address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->tinyInteger('star_rating')->nullable();
            $table->text('description');
            $table->string('img_url')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->boolean('archived')->default(0);
            $table->timestamps();
        });

        Schema::create('tbl_room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->tinyInteger('capacity')->default(2);
            $table->boolean('archived')->default(0);
            $table->timestamps();
        });

        Schema::create('tbl_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_hotel')->constrained('tbl_hotels');
            $table->foreignId('id_room_type')->constrained('tbl_room_types');
            $table->string('room_number');
            $table->string('slug')->unique();
            $table->decimal('price', 12, 0);
            $table->text('description')->nullable();
            $table->string('img_url')->nullable();
            $table->enum('status', ['available', 'maintenance'])->default('available');
            $table->boolean('archived')->default(0);
            $table->timestamps();
            $table->index(['id_hotel', 'status']);
        });

        Schema::create('tbl_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('id_user')->constrained('tbl_users');
            $table->foreignId('id_room')->constrained('tbl_rooms');
            $table->string('customer_name');
            $table->tinyInteger('guest_count')->default(1);
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->tinyInteger('total_nights');
            $table->decimal('total_price', 12, 0);
            $table->enum('status', ['draft', 'pending', 'paid', 'cancelled', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->decimal('price_per_night', 12, 0);
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->enum('cancel_reason', ['customer', 'admin', 'expired'])->nullable();
            $table->boolean('archived')->default(0);
            $table->timestamps();
            $table->index(['id_room', 'check_in_date', 'check_out_date']);
            $table->index(['status', 'expires_at']);
            $table->index('status');
        });

        Schema::create('tbl_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_booking')->constrained('tbl_bookings');
            $table->enum('payment_method', ['bank_transfer', 'ewallet_sim', 'cash']);
            $table->decimal('amount', 12, 0);
            $table->dateTime('payment_date')->nullable();
            $table->string('proof_url')->nullable();
            $table->string('reference_code')->unique();
            $table->foreignId('verified_by')->nullable()->constrained('tbl_users');
            $table->dateTime('verified_at')->nullable();
            $table->string('reject_reason')->nullable();
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->boolean('archived')->default(0);
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('tbl_hotel_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_hotel')->constrained('tbl_hotels');
            $table->string('img_url');
            $table->tinyInteger('sort_order')->default(0);
            $table->boolean('archived')->default(0);
            $table->timestamps();
        });

        Schema::create('tbl_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->boolean('archived')->default(0);
            $table->timestamps();
        });

        Schema::create('tbl_hotel_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_hotel')->constrained('tbl_hotels');
            $table->foreignId('id_facility')->constrained('tbl_facilities');
            $table->timestamps();
            $table->unique(['id_hotel', 'id_facility']);
        });

        Schema::create('tbl_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_hotel')->constrained('tbl_hotels');
            $table->foreignId('id_user')->constrained('tbl_users');
            $table->foreignId('id_booking')->constrained('tbl_bookings');
            $table->tinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('archived')->default(0);
            $table->timestamps();
            $table->unique(['id_booking']);
        });

        Schema::create('tbl_whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_booking')->nullable()->constrained('tbl_bookings');
            $table->string('phone');
            $table->text('message');
            $table->string('status')->default('queued');
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_whatsapp_logs');
        Schema::dropIfExists('tbl_reviews');
        Schema::dropIfExists('tbl_hotel_facilities');
        Schema::dropIfExists('tbl_facilities');
        Schema::dropIfExists('tbl_hotel_images');
        Schema::dropIfExists('tbl_payments');
        Schema::dropIfExists('tbl_bookings');
        Schema::dropIfExists('tbl_rooms');
        Schema::dropIfExists('tbl_room_types');
        Schema::dropIfExists('tbl_hotels');
    }
};
