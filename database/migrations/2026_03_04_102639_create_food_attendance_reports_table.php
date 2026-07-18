<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('food_attendance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('meal_date');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner']);
            $table->enum('status', ['normal', 'late', 'absent'])->default('normal');
            $table->timestamps();

            $table->unique(['student_id', 'meal_date', 'meal_type'], 'student_meal_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_attendance_reports');
    }
};
