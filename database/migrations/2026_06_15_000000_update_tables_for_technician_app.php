<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add password, latitude, longitude to technicians
        Schema::table('technicians', function (Blueprint $table) {
            if (!Schema::hasColumn('technicians', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (!Schema::hasColumn('technicians', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('fcm_token');
            }
            if (!Schema::hasColumn('technicians', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
        });

        // Add before_photo, after_photo to bookings
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'before_photo')) {
                $table->string('before_photo')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('bookings', 'after_photo')) {
                $table->string('after_photo')->nullable()->after('before_photo');
            }
        });

        // Create chat_messages table
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->enum('sender_type', ['customer', 'technician']);
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['before_photo', 'after_photo']);
        });

        Schema::table('technicians', function (Blueprint $table) {
            $table->dropColumn(['password', 'latitude', 'longitude']);
        });
    }
};
