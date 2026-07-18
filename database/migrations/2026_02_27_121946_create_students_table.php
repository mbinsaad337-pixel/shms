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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->string('student_number')->unique();
            $table->string('national_id')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->date('date_of_birth');
            $table->string('nationality');
            $table->string('blood_type')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('photo')->nullable();
            $table->string('university')->nullable();
            $table->string('college')->nullable();
            $table->string('major')->nullable();
            $table->string('academic_level')->nullable();
            $table->string('university_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('emergency_name')->nullable();
            $table->string('emergency_relation')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->text('barcode')->nullable();
            $table->enum('status', ['pending', 'active', 'suspended', 'expelled'])->default('pending');
            $table->date('registration_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
