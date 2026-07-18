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
        Schema::table('food_subscriptions', function (Blueprint $table) {
            $table->string('rejection_reason', 255)->nullable()->after('suspended_reason');
            // Change enum to include pending and rejected
            $table->string('status')->default('active')->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_subscriptions', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
