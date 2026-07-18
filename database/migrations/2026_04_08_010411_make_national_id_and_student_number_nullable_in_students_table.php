<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('national_id')->nullable()->change();
            $table->string('student_number')->nullable()->change();
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('national_id')->nullable(false)->change();
            $table->string('student_number')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
        });
    }
};
