<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('comment');
        });

        // Migrate existing data: is_approved = true -> status = 'approved', otherwise 'pending'
        DB::statement("UPDATE reviews SET status = CASE WHEN is_approved = 1 THEN 'approved' ELSE 'pending' END");
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
