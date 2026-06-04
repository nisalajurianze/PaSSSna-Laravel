<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'total')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('total', 10, 2)->nullable()->after('total_amount');
            });
        }

        if (Schema::hasColumn('orders', 'total')) {
            DB::statement('UPDATE orders SET total = total_amount WHERE total IS NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'total')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('total');
            });
        }
    }
};
