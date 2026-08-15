<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_budgets', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('notes');
        });

        Schema::table('monthly_settlements', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('monthly_budgets', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });

        Schema::table('monthly_settlements', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
