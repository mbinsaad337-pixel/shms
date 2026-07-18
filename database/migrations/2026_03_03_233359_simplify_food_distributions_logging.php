<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('food_distributions', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->change();
            $table->string('group_name')->nullable()->after('student_qr_group_id');
            $table->integer('group_members_count')->nullable()->after('group_name');
        });
    }

    public function down(): void
    {
        Schema::table('food_distributions', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable(false)->change();
            $table->dropColumn(['group_name', 'group_members_count']);
        });
    }
};
