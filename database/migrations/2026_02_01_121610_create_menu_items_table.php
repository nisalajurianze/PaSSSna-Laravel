<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->json('sizes')->nullable(); // {'regular': 10, 'medium': 12, 'large': 15}
            $table->json('flavors')->nullable(); // Available flavors
            $table->json('extra_toppings')->nullable(); // {'extra_cheese': 1.5, 'bacon': 2.0}
            $table->enum('category', ['appetizer', 'main_course', 'dessert', 'beverage', 'special', 'custom']);
            $table->enum('food_type', ['vegetarian', 'non_vegetarian', 'vegan'])->default('non_vegetarian');
            $table->integer('preparation_time')->default(15); // minutes
            $table->boolean('is_available')->default(true);
            $table->boolean('is_fast_moving')->default(false);
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_customizable')->default(false);
            $table->json('ingredients')->nullable();
            $table->json('nutrition_info')->nullable();
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->date('offer_valid_from')->nullable();
            $table->date('offer_valid_to')->nullable();
            $table->integer('min_order_qty')->default(1);
            $table->integer('max_order_qty')->default(10);
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('total_orders')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
