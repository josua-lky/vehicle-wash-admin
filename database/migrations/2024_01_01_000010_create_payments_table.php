<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('payment_method', ['va_bank','ewallet','qris','credit_card','cod']);
            $table->string('payment_provider', 50)->nullable();
            $table->string('transaction_id', 100)->nullable()->unique();
            $table->enum('status', ['pending','paid','failed','refunded','expired'])->default('pending');
            $table->boolean('refund_requested')->default(false);
            $table->decimal('amount',        12, 2);
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();
            $table->index(['status','created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('payments'); }
};
