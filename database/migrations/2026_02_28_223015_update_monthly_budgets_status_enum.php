<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('monthly_budgets', function (Blueprint $table) {
            // In MySQL/PostgreSQL we often need raw SQL for enum changes or just redefine it
            // For now, let's try to redefine it if the driver supports it, or use raw for MySQL
            if (config('database.default') === 'mysql') {
                DB::statement("ALTER TABLE monthly_budgets MODIFY COLUMN status ENUM('draft', 'submitted', 'confirmed', 'approved', 'partially_approved', 'rejected') DEFAULT 'draft'");
            } else {
                $table->string('status')->change(); // fallback
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_budgets', function (Blueprint $table) {
            if (config('database.default') === 'mysql') {
                DB::statement("ALTER TABLE monthly_budgets MODIFY COLUMN status ENUM('draft', 'submitted', 'approved', 'partially_approved', 'rejected') DEFAULT 'draft'");
            }
        });
    }
};
