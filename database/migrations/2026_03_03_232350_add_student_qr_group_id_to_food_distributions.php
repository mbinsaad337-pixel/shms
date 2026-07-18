<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('food_distributions', function (Blueprint $table) {
            $table->foreignId('student_qr_group_id')->nullable()->after('qr_group_id')->constrained('student_qr_groups')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('food_distributions', function (Blueprint $table) {
            $table->dropForeign(['student_qr_group_id']);
            $table->dropColumn('student_qr_group_id');
        });
    }
};
