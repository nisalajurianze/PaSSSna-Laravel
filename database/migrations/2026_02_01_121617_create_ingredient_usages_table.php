<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredient_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventory')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('menu_item_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('custom_ingredient_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('quantity_used', 10, 3);
            $table->enum('usage_type', ['order', 'wastage', 'adjustment', 'spoilage']);
            $table->date('usage_date');
            $table->string('unit');
            $table->decimal('cost', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_usages');
    }
};
