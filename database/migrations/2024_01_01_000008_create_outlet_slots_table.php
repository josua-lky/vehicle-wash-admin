<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wash_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->date('slot_date');
            $table->time('slot_time');
            $table->unsignedTinyInteger('capacity')->default(3);
            $table->unsignedTinyInteger('booked_count')->default(0);
            $table->enum('status', ['available','blocked'])->default('available');
            $table->timestamps();
            $table->unique(['outlet_id','slot_date','slot_time']);
            $table->index(['outlet_id','slot_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('wash_slots'); }
};
