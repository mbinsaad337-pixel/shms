<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->string('absence_type')->nullable()->after('has_excuse')->comment('housing, quran, activity etc');
        });
    }

    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->dropColumn(['absence_type']);
        });
    }
};
