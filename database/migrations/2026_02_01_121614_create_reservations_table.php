<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->date('reservation_date');
            $table->time('reservation_time');
            $table->integer('duration_minutes')->default(90);
            $table->integer('number_of_people');
            $table->json('table_numbers'); // Array of table numbers
            $table->enum('status', ['pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->enum('reservation_type', ['regular', 'special_occasion', 'business', 'family'])->default('regular');
            $table->text('special_requests')->nullable();
            $table->text('occasion_notes')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->text('confirmation_message')->nullable();
            $table->timestamp('arrival_time')->nullable();
            $table->timestamp('seated_time')->nullable();
            $table->timestamp('departure_time')->nullable();
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->boolean('deposit_paid')->default(false);
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
