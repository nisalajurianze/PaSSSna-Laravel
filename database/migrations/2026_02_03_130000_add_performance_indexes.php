<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('order_number');
            $table->index('payment_status');
            $table->index('order_type');
        });

        // Reservations table indexes
        Schema::table('reservations', function (Blueprint $table) {
            $table->index(['reservation_date', 'reservation_time']);
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index('customer_phone');
        });

        // Menu items table indexes
        Schema::table('menu_items', function (Blueprint $table) {
            $table->index(['category', 'is_available']);
            $table->index('is_fast_moving');
            $table->index('is_recommended');
            $table->index(['is_available', 'is_fast_moving']);
            $table->index('name');
        });

        // Order items table indexes
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('menu_item_id');
        });

        // Inventory table indexes
        Schema::table('inventory', function (Blueprint $table) {
            $table->index(['current_quantity', 'minimum_quantity']);
            $table->index('item_name');
        });

        // Staff table indexes
        Schema::table('staff', function (Blueprint $table) {
            $table->index('role');
            $table->index('status');
        });

        // Dining sessions indexes
        Schema::table('dining_sessions', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
            $table->index('status');
        });

        // Contact messages indexes
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->index('status');
            $table->index(['created_at', 'status']);
        });

        // Reviews indexes
        Schema::table('reviews', function (Blueprint $table) {
            $table->index('menu_item_id');
            $table->index('rating');
        });

        // Payments indexes
        Schema::table('payments', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('status');
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['order_number']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['order_type']);
        });

        // Reservations table indexes
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['reservation_date', 'reservation_time']);
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['customer_phone']);
        });

        // Menu items table indexes
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['category', 'is_available']);
            $table->dropIndex(['is_fast_moving']);
            $table->dropIndex(['is_recommended']);
            $table->dropIndex(['is_available', 'is_fast_moving']);
            $table->dropIndex(['name']);
        });

        // Order items table indexes
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['menu_item_id']);
        });

        // Inventory table indexes
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropIndex(['current_quantity', 'minimum_quantity']);
            $table->dropIndex(['item_name']);
        });

        // Staff table indexes
        Schema::table('staff', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['status']);
        });

        // Dining sessions indexes
        Schema::table('dining_sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['status']);
        });

        // Contact messages indexes
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at', 'status']);
        });

        // Reviews indexes
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['menu_item_id']);
            $table->dropIndex(['rating']);
        });

        // Payments indexes
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['status', 'created_at']);
        });
    }
};
