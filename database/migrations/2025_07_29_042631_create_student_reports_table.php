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
        Schema::create('student_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('restrict');
            $table->enum('semester', ['1', '2']);
            $table->integer('class_rank')->nullable();
            $table->decimal('average_grade', 5, 2)->nullable(); // 85.50
            $table->integer('total_school_days');
            $table->integer('present_days')->default(0);
            $table->integer('sick_days')->default(0);
            $table->integer('permission_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->text('homeroom_teacher_notes')->nullable();
            $table->text('principal_notes')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('publish_date')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id', 'semester']);
            $table->index(['academic_year_id', 'semester', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_reports');
    }
};
