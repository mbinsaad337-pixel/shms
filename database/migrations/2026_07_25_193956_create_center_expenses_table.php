<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('center_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['rent', 'water', 'electricity', 'internet', 'other'])->comment('نوع المصروف: إيجار سكن، فاتورة ماء، فاتورة كهرباء، فاتورة إنترنت، مصروفات أخرى');
            $table->decimal('amount', 10, 2)->comment('المبلغ المستحق');
            $table->date('due_date')->comment('تاريخ الاستحقاق');
            $table->date('payment_date')->nullable()->comment('تاريخ الدفع الفعلي');
            $table->enum('status', ['pending', 'paid'])->default('pending')->comment('حالة الدفع');
            $table->string('receipt')->nullable()->comment('مسار المرفق / إيصال الدفع');
            $table->string('receipt_type')->nullable()->comment('نوع المرفق: pdf أو صورة');
            $table->integer('month')->comment('شهر الاستحقاق');
            $table->integer('year')->comment('سنة الاستحقاق');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('center_expenses');
    }
};
