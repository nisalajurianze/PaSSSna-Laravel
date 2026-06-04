<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->date('shift_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('shift_type', ['morning', 'afternoon', 'evening', 'night', 'split'])->default('morning');
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled', 'absent'])->default('scheduled');
            $table->string('location')->nullable();
            $table->json('assigned_tables')->nullable(); // Tables assigned for wait staff
            $table->json('assigned_duties')->nullable(); // Specific duties
            $table->text('notes')->nullable();
            $table->decimal('hours_worked', 5, 2)->nullable();
            $table->timestamp('clock_in_time')->nullable();
            $table->timestamp('clock_out_time')->nullable();
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['staff_id', 'shift_date', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_schedules');
    }
};
