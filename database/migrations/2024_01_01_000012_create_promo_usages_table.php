<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promo_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->decimal('discount_applied', 12, 2);
            $table->timestamps();
            $table->unique(['promo_id','booking_id']);
            $table->index(['promo_id','customer_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('promo_usages'); }
};
