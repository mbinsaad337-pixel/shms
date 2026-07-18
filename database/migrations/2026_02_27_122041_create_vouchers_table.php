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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->string('voucher_number')->unique();
            $table->enum('type', ['receipt', 'payment', 'transfer', 'salary', 'purchase_invoice', 'sales_invoice']);
            $table->foreignId('fund_id')->constrained('funds')->onDelete('cascade');
            $table->foreignId('target_fund_id')->nullable()->constrained('funds')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('amount_text')->nullable();
            $table->date('date');
            $table->string('payee_or_payer')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'approved', 'rejected'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
