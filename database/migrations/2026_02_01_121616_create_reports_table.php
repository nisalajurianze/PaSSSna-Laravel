<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique();
            $table->enum('report_type', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly', 'custom']);
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->json('data'); // Store report data as JSON
            $table->json('summary'); // Store summary metrics
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable(); // PDF file path
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
