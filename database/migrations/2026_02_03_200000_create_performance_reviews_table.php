<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->date('review_date');
            $table->integer('punctuality_score');
            $table->integer('efficiency_score');
            $table->integer('customer_feedback_score');
            $table->integer('teamwork_score');
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->index(['staff_id', 'review_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};
