<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('vehicle_name', 100)->nullable();
            $table->enum('vehicle_type', ['roda_2','roda_4'])->default('roda_4');
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('service_type', ['home','outlet']);
            $table->foreignId('outlet_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('outlet_slot_id')->nullable()->references('id')->on('wash_slots')->nullOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained()->nullOnDelete();
            $table->text('service_address')->nullable();
            $table->decimal('latitude',  10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->dateTime('scheduled_at');
            $table->enum('status', ['pending','confirmed','assigned','on_way','in_progress','completed','cancelled'])->default('pending');
            $table->foreignId('promo_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('subtotal',       12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount',   12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['status','scheduled_at']);
            $table->index(['customer_id','status']);
        });
    }
    public function down(): void { Schema::dropIfExists('bookings'); }
};
