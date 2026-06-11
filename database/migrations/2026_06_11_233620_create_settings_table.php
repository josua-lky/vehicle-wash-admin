<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        DB::table('settings')->insert([
            ['key' => 'app_name', 'value' => 'Vehicle Wash', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_email', 'value' => 'info@vehiclewash.id', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'whatsapp', 'value' => '08112345678', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'service_radius', 'value' => '15', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'delivery_rate', 'value' => '2000', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'notify_new_booking', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'notify_payment_received', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'notify_booking_cancelled', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'notify_bad_rating', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
