<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('item_code')->unique();
            $table->enum('category', ['vegetable', 'meat', 'dairy', 'spice', 'grain', 'beverage', 'other']);
            $table->enum('unit', ['kg', 'g', 'l', 'ml', 'piece', 'pack', 'dozen'])->default('piece');
            $table->decimal('current_quantity', 10, 3);
            $table->decimal('minimum_quantity', 10, 3);
            $table->decimal('maximum_quantity', 10, 3);
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_value', 10, 2);
            $table->enum('status', ['in_stock', 'low_stock', 'out_of_stock', 'expired'])->default('in_stock');
            $table->string('supplier_name')->nullable();
            $table->string('supplier_contact')->nullable();
            $table->date('last_restocked_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('storage_location')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('reorder_quantity', 10, 3);
            $table->decimal('daily_usage_rate', 10, 3)->default(0);
            $table->integer('days_of_supply')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
