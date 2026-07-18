<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('food_purchase_invoices', function (Blueprint $table) {
            $table->enum('payment_type', ['cash', 'credit'])->default('credit')->after('total_amount');
        });
    }

    public function down(): void
    {
        Schema::table('food_purchase_invoices', function (Blueprint $table) {
            $table->dropColumn('payment_type');
        });
    }
};
