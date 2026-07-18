<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_budget_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('food_budgets')->onDelete('cascade');
            $table->string('item_name'); // البيان
            $table->integer('days')->nullable(); // عدد الأيام
            $table->decimal('quantity', 15, 3)->nullable(); // الكمية
            $table->decimal('unit_price', 15, 2)->nullable(); // سعر الوحدة
            $table->decimal('total', 15, 2)->default(0); // الإجمالي (محسوب)
            $table->string('supplier_name')->nullable(); // المورد
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_budget_lines');
    }
};
