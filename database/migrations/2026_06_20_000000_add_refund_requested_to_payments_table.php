<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('payments', 'refund_requested')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->boolean('refund_requested')->default(false)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('payments', 'refund_requested')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('refund_requested');
            });
        }
    }
};
