<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number')->unique();
            $table->string('name')->nullable();
            $table->enum('type', ['indoor', 'outdoor', 'private_room', 'bar'])->default('indoor');
            $table->integer('capacity');
            $table->integer('min_capacity')->nullable();
            $table->string('location')->nullable();
            $table->string('area')->nullable();
            $table->enum('status', ['available', 'reserved', 'occupied', 'cleaning', 'maintenance'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->text('features')->nullable(); // JSON or text for features
            $table->decimal('reservation_fee', 10, 2)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
