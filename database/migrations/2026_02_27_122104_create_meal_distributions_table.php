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
        Schema::create('meal_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained('meal_subscriptions')->onDelete('cascade');
            $table->string('meal_type'); // breakfast, lunch, dinner
            $table->timestamp('distributed_at');
            $table->foreignId('distributed_by')->constrained('users');
            $table->foreignId('group_id')->nullable(); // group distribution
            $table->string('plate_barcode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_distributions');
    }
};
