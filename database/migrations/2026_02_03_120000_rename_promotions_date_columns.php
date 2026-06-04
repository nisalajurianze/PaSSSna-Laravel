<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename valid_from to start_date and valid_to to end_date
        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('valid_from', 'start_date');
            $table->renameColumn('valid_to', 'end_date');
        });
    }

    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('start_date', 'valid_from');
            $table->renameColumn('end_date', 'valid_to');
        });
    }
};
