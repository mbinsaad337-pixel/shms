<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('food_subscriptions')->onDelete('set null');
            $table->foreignId('qr_group_id')->nullable()->constrained('food_qr_groups')->onDelete('set null');
            $table->string('meal_type'); // breakfast, lunch, dinner
            $table->enum('distribution_type', ['individual', 'group', 'extra'])->default('individual');
            $table->string('dish_number')->nullable(); // رقم الصحن
            $table->string('scan_type')->nullable(); // individual_qr, group_qr
            $table->foreignId('distributed_by')->constrained('users');
            $table->timestamp('distributed_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_distributions');
    }
};
