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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('nis', 20)->unique(); // NIS
            $table->string('national_student_id', 20)->unique()->nullable(); // NISN
            $table->string('full_name', 100);
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('father_name', 100)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('father_occupation', 100)->nullable();
            $table->string('mother_occupation', 100)->nullable();
            $table->date('enrollment_date');
            $table->enum('status', ['active', 'graduated', 'transferred', 'dropout'])->default('active');
            $table->timestamps();

            $table->index('nis');
            $table->index('national_student_id');
            $table->index(['status', 'enrollment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
