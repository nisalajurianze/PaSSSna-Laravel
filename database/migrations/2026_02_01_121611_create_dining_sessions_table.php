<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dining_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_code')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('table_number');
            $table->integer('number_of_people');
            $table->enum('status', ['active', 'closed', 'payment_pending', 'cancelled'])->default('active');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->decimal('total_bill', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('remaining_balance', 10, 2)->default(0);
            $table->boolean('payment_completed')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('assigned_waiter')->nullable()->constrained('staff')->onDelete('set null');
            $table->json('custom_meal_preferences')->nullable();
            $table->string('exit_password')->nullable(); // Password to exit dining section
            $table->boolean('exit_with_admin_password')->default(false);
            $table->timestamp('last_order_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dining_sessions');
    }
};
