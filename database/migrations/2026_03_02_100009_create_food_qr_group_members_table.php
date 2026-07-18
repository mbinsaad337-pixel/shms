<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_qr_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('food_qr_groups')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('food_subscriptions')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_qr_group_members');
    }
};
