<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique();
            $table->enum('payment_method', ['cash', 'card', 'cash_on_delivery', 'online', 'mobile_wallet']);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded', 'partially_refunded'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->string('currency')->default('USD');
            $table->json('payment_details')->nullable(); // Card details, online payment response
            $table->string('card_last_four')->nullable();
            $table->string('card_brand')->nullable();
            $table->timestamp('payment_date');
            $table->timestamp('refund_date')->nullable();
            $table->text('payment_notes')->nullable();
            $table->string('receipt_url')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('payer_phone')->nullable();
            $table->string('gateway_response_code')->nullable();
            $table->text('gateway_response_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
