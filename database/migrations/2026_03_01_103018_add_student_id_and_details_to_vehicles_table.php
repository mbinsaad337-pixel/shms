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
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'student_id')) {
                $table->foreignId('student_id')->nullable()->after('center_id')->constrained('students')->onDelete('cascade');
            }
            if (!Schema::hasColumn('vehicles', 'color')) {
                $table->string('color')->nullable()->after('plate_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'student_id')) {
                $table->dropForeign(['student_id']);
                $table->dropColumn('student_id');
            }
            if (Schema::hasColumn('vehicles', 'color')) {
                $table->dropColumn('color');
            }
        });
    }
};
