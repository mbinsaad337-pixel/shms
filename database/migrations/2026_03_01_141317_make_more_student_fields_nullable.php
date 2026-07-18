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
        Schema::table('students', function (Blueprint $table) {
            $table->string('university')->nullable()->change();
            $table->string('college')->nullable()->change();
            $table->string('major')->nullable()->change();
            $table->string('academic_level')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('university')->nullable(false)->change();
            $table->string('college')->nullable(false)->change();
            $table->string('major')->nullable(false)->change();
            $table->string('academic_level')->nullable(false)->change();
        });
    }
};
