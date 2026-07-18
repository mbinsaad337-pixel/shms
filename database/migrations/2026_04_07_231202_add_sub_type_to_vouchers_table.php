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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('sub_type')->nullable()->after('type')->comment('housing, deposit');
        });

        // Update existing receipts to 'deposit' by default
        \Illuminate\Support\Facades\DB::table('vouchers')
            ->where('type', 'receipt')
            ->update(['sub_type' => 'deposit']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('sub_type');
        });
    }
};
