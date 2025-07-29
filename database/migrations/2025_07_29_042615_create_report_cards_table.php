<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('restrict');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('restrict');
            $table->enum('semester', ['1', '2']);
            $table->decimal('knowledge_grade', 5, 2)->nullable(); // 85.50
            $table->decimal('skill_grade', 5, 2)->nullable(); // 85.50
            $table->enum('attitude_grade', ['A', 'B', 'C', 'D'])->nullable();
            $table->text('teacher_notes')->nullable();
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->timestamps();

            $table->unique(['student_id', 'subject_id', 'class_id', 'academic_year_id', 'semester'], 'report_card_unique');
            $table->index(['class_id', 'academic_year_id', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};
