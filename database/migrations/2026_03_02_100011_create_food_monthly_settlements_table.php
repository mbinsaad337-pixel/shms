<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_monthly_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->foreignId('budget_id')->nullable()->constrained('food_budgets')->onDelete('set null');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('total_revenue', 15, 2)->default(0);    // إجمالي الاشتراكات
            $table->decimal('total_expenses', 15, 2)->default(0);   // إجمالي المصاريف
            $table->decimal('net_result', 15, 2)->default(0);       // صافي النتيجة
            $table->enum('result_type', ['surplus', 'deficit', 'break_even'])->default('break_even');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_monthly_settlements');
    }
};
