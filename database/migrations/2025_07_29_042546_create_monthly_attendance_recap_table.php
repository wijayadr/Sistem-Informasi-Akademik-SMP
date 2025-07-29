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
        Schema::create('monthly_attendance_recap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->integer('month'); // 1-12
            $table->integer('year'); // 2024
            $table->integer('present_count')->default(0);
            $table->integer('sick_count')->default(0);
            $table->integer('permission_count')->default(0);
            $table->integer('absent_count')->default(0);
            $table->integer('total_effective_days');
            $table->decimal('attendance_percentage', 5, 2)->default(0); // 95.50
            $table->timestamps();

            $table->unique(['student_id', 'class_id', 'month', 'year']);
            $table->index(['class_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_attendance_recap');
    }
};
