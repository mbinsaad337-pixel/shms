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
        Schema::create('settlement_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('settlement_id')->constrained('monthly_settlements')->onDelete('cascade');
            $table->foreignId('fund_id')->constrained('funds')->onDelete('cascade');
            $table->decimal('opening_balance', 15, 2);
            $table->decimal('total_income', 15, 2)->default(0);
            $table->decimal('total_expense', 15, 2)->default(0);
            $table->decimal('transfers_in', 15, 2)->default(0);
            $table->decimal('transfers_out', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlement_details');
    }
};
