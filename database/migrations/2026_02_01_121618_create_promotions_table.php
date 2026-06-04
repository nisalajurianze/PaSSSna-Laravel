<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('promo_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed', 'free_item', 'buy_one_get_one']);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->integer('maximum_uses')->nullable();
            $table->integer('uses_per_customer')->default(1);
            $table->integer('times_used')->default(0);
            $table->date('valid_from');
            $table->date('valid_to');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_visible')->default(true);
            $table->json('applicable_categories')->nullable();
            $table->json('excluded_items')->nullable();
            $table->foreignId('free_item_id')->nullable()->constrained('menu_items')->onDelete('set null');
            $table->integer('free_item_quantity')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
