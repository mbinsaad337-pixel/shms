<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->string('title')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->integer('days_count')->default(30);
            $table->integer('subscribers_count')->default(0);
            $table->decimal('cost_per_student', 15, 2)->nullable();
            $table->decimal('daily_rate', 15, 2)->nullable(); // قيمة الاشتراك اليومي
            $table->date('last_payment_date')->nullable(); // آخر تاريخ لدفع الاشتراك
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_budgets');
    }
};
