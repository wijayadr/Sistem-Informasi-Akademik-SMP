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
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('teacher_subject_id')->constrained('teacher_subjects')->onDelete('cascade');
            $table->foreignId('grade_component_id')->constrained('grade_components')->onDelete('restrict');
            $table->decimal('grade_value', 5, 2); // 85.50
            $table->date('input_date');
            $table->text('notes')->nullable();
            $table->foreignId('input_teacher_id')->constrained('teachers')->onDelete('restrict');
            $table->timestamps();

            $table->index(['student_id', 'teacher_subject_id']);
            $table->index(['input_date', 'input_teacher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};
