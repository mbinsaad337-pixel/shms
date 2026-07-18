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
        Schema::table('students', function (Blueprint $table) {
            $table->decimal('annual_fees', 15, 2)->default(0)->after('status');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete()->after('fund_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('student_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('annual_fees');
        });
    }
};
