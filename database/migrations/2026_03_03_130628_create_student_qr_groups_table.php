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
        Schema::create('student_qr_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('primary_student_id')->constrained('students')->onDelete('cascade');
            $table->string('group_token')->unique();
            $table->text('json_data')->nullable(); // Stores the student data JSON
            $table->boolean('is_link_only')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('active'); // active, cancelled, used
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_qr_groups');
    }
};
