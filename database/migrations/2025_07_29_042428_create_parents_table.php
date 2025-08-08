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
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('full_name', 100);
            $table->enum('relationship', ['father', 'mother', 'guardian']);
            $table->string('phone_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('occupation', 100)->nullable();
            $table->timestamps();

            $table->index(['student_id', 'relationship']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
