<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->string('voucher_number');
            $table->enum('type', ['payment', 'receipt']); // صرف أو قبض
            $table->date('voucher_date');
            $table->foreignId('supplier_id')->nullable()->constrained('food_suppliers')->onDelete('set null');
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->string('attachment')->nullable();
            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_vouchers');
    }
};
