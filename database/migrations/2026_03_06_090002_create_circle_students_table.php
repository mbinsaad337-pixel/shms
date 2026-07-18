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
        Schema::create('circle_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained('quran_circles')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('last_sura')->nullable();
            $table->integer('last_verse')->nullable();
            $table->timestamp('last_progress_at')->nullable();
            $table->timestamps();

            // Allow a student to be in multiple circles if needed, OR just one?
            // Usually, unique circle_id + student_id
            $table->unique(['circle_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circle_students');
    }
};
