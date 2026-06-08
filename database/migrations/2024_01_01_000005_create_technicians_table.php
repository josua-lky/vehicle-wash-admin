<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone', 20)->unique();
            $table->string('profile_photo')->nullable();
            $table->string('specialization')->default('motor');
            $table->string('area', 100)->nullable();
            $table->foreignId('outlet_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['active','inactive','cuti','busy'])->default('active');
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_orders')->default(0);
            $table->date('join_date')->nullable();
            $table->string('fcm_token')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('technicians'); }
};
