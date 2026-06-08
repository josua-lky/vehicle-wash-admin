<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('phone', 20)->nullable();
            $table->decimal('latitude',  10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedTinyInteger('capacity_per_hour')->default(3);
            $table->time('open_time')->default('07:00:00');
            $table->time('close_time')->default('20:00:00');
            $table->string('photo')->nullable();
            $table->enum('status', ['active','inactive','maintenance'])->default('active');
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('outlets'); }
};
