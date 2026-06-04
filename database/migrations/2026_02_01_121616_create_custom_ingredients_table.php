<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['base', 'protein', 'vegetable', 'sauce', 'topping', 'cheese', 'spice']);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('calories')->nullable();
            $table->enum('food_type', ['vegetarian', 'non_vegetarian', 'vegan'])->default('vegetarian');
            $table->boolean('is_spicy')->default(false);
            $table->boolean('is_gluten_free')->default(false);
            $table->boolean('is_dairy_free')->default(false);
            $table->boolean('is_nut_free')->default(false);
            $table->boolean('is_available')->default(true);
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_ingredients');
    }
};
