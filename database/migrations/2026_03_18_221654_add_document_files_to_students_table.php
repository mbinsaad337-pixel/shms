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
            $table->string('id_card_file')->nullable()->after('id_card_date');
            $table->string('certificate_file')->nullable()->after('last_certificate');
            $table->string('university_card_file')->nullable()->after('university_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['id_card_file', 'certificate_file', 'university_card_file']);
        });
    }
};
