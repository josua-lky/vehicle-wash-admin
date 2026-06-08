<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone', 20)->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->string('profile_photo')->nullable();
            $table->enum('status', ['active','inactive','banned'])->default('active');
            $table->string('fcm_token')->nullable();
            $table->string('google_id')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('customers'); }
};
