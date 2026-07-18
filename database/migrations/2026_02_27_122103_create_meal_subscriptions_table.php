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
        Schema::create('meal_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->string('type'); // monthly, semester, annual
            $table->json('meal_types'); // breakfast, lunch, dinner
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['active', 'suspended', 'expired', 'cancelled'])->default('active');
            $table->text('suspended_reason')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_subscriptions');
    }
};
