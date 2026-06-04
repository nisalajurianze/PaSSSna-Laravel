<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dateTime('start_at')->nullable()->after('reservation_time');
            $table->dateTime('end_at')->nullable()->after('start_at');

            $table->index(['start_at', 'end_at']);
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['start_at', 'end_at']);
            $table->dropColumn(['start_at', 'end_at']);
        });
    }
};

