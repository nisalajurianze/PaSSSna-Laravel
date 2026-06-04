<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('image_url')->nullable();
            $table->integer('points_required');
            $table->string('reward_type'); // discount_percent, discount_amount, free_item
            $table->decimal('reward_value', 10, 2)->default(0); // Percentage or fixed amount
            $table->decimal('minimum_order_amount', 10, 2)->default(0);
            $table->integer('max_uses')->nullable(); // null = unlimited
            $table->integer('current_uses')->default(0);
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('loyalty_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('loyalty_reward_id')->constrained()->onDelete('cascade');
            $table->string('promo_code')->nullable(); // Generated promo code if applicable
            $table->integer('points_used');
            $table->string('status')->default('pending'); // pending, used, expired
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_redemptions');
        Schema::dropIfExists('loyalty_rewards');
    }
};
