<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("
                ALTER TABLE users
                MODIFY role ENUM(
                    'customer',
                    'super_admin',
                    'admin_operasional',
                    'admin_outlet',
                    'admin_keuangan'
                )
                NOT NULL
                DEFAULT 'customer'
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("
                ALTER TABLE users
                MODIFY role ENUM(
                    'super_admin',
                    'admin_operasional',
                    'admin_outlet',
                    'admin_keuangan'
                )
            ");
        }
    }
};