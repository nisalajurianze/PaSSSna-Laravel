<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement(
            "ALTER TABLE `orders` MODIFY COLUMN `status` ENUM(
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'out_for_delivery',
                'delivered',
                'served',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'"
        );
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement(
            "ALTER TABLE `orders` MODIFY COLUMN `status` ENUM(
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'served',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'"
        );
    }
};

