<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_table', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('table_id')->constrained('tables')->onDelete('cascade');

            $table->unique(['reservation_id', 'table_id']);
            $table->index(['table_id', 'reservation_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_table');
    }
};

