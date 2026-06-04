<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('menu_item_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('rating'); // 1-5
            $table->string('title')->nullable();
            $table->text('comment');
            $table->enum('review_type', ['food', 'service', 'ambiance', 'overall'])->default('overall');
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->integer('report_count')->default(0);
            $table->json('photos')->nullable();
            $table->string('reviewer_name')->nullable();
            $table->string('reviewer_email')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
