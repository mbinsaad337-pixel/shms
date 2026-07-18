<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_qr_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->foreignId('created_by_student_id')->constrained('students')->onDelete('cascade');
            $table->string('qr_code'); // QR content
            $table->string('qr_token')->unique(); // Secure token
            $table->integer('members_count')->default(0);
            $table->date('valid_date'); // صالح ليوم واحد
            $table->boolean('is_used')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_qr_groups');
    }
};
