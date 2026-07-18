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
        Schema::table('food_budgets', function (Blueprint $table) {
            if (!Schema::hasColumn('food_budgets', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        Schema::table('food_suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('food_suppliers', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        Schema::table('food_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('food_subscriptions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        Schema::table('food_purchase_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('food_purchase_invoices', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_budgets', function (Blueprint $table) {
            if (Schema::hasColumn('food_budgets', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
        Schema::table('food_suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('food_suppliers', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
        Schema::table('food_subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('food_subscriptions', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
        Schema::table('food_purchase_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('food_purchase_invoices', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
