<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Technician;

return new class extends Migration {
    public function up(): void
    {
        foreach (Technician::all() as $tech) {
            $tech->updateRating();
        }
    }

    public function down(): void
    {
        // No action needed for rollback
    }
};
