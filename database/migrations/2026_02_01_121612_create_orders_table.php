<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('order_type', ['dine_in', 'takeaway', 'delivery']);
            $table->integer('table_number')->nullable();
            $table->foreignId('dining_session_id')->nullable()->constrained('dining_sessions')->onDelete('set null');
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'served', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['cash', 'card', 'cash_on_delivery', 'online'])->default('cash');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('promo_code')->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('special_instructions')->nullable();
            $table->integer('estimated_preparation_time')->nullable(); // minutes
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('preparation_started_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
