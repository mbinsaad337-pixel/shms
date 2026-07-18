<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('budget_id')->nullable()->constrained('food_budgets')->onDelete('set null');
            $table->enum('subscription_type', ['daily', 'semi_monthly', 'monthly'])->default('monthly');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count')->default(30);
            $table->date('last_payment_date')->nullable(); // آخر يوم لدفع الاشتراك
            $table->decimal('daily_rate', 15, 2)->default(0); // قيمة الاشتراك اليومي
            $table->decimal('total_due', 15, 2)->default(0);  // المبلغ المستحق (مدين)
            $table->decimal('total_paid', 15, 2)->default(0); // المبلغ المدفوع (دائن)
            $table->enum('status', ['active', 'suspended', 'expired', 'cancelled'])->default('active');
            $table->string('suspended_reason')->nullable();
            $table->string('qr_code')->nullable(); // QR code للطالب
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_subscriptions');
    }
};
