<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_item_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_custom_meal')->default(false);
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('size')->nullable();
            $table->string('flavor')->nullable();
            $table->json('selected_toppings')->nullable();
            $table->json('custom_ingredients')->nullable(); // For custom meals
            $table->text('special_instructions')->nullable();
            $table->boolean('is_prepared')->default(false);
            $table->timestamp('prepared_at')->nullable();
            $table->foreignId('prepared_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
