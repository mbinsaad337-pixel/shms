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
        Schema::create('circle_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('circle_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['present', 'absent'])->default('absent');
            $table->string('sura')->nullable(); // Optional per-session progress
            $table->integer('verse')->nullable(); // Optional per-session progress
            $table->timestamps();

            $table->unique(['session_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circle_attendance');
    }
};
