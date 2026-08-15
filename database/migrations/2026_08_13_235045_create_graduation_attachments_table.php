<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graduation_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('name');        // اسم المرفق الذي يسميه الطالب
            $table->string('file_path');   // مسار الملف
            $table->string('file_type')->nullable(); // mime type
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduation_attachments');
    }
};
