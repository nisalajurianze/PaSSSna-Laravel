<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('password');
            $table->string('photo')->nullable();
            $table->enum('role', ['admin', 'manager', 'chef', 'waiter', 'bartender', 'host', 'cashier', 'delivery_boy'])->default('waiter');
            $table->enum('status', ['active', 'inactive', 'on_leave', 'terminated'])->default('active');
            $table->decimal('salary', 10, 2)->default(0);
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->text('address')->nullable();
            $table->text('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->text('notes')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
