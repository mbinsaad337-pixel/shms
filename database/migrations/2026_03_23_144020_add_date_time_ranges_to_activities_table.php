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
        Schema::table('activities', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('activity_date');
            $table->time('end_time')->nullable()->after('time');
            $table->renameColumn('time', 'start_time');
            $table->renameColumn('activity_date', 'start_date');
        });

        Schema::create('activity_student_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_student_targets');
        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('start_date', 'activity_date');
            $table->renameColumn('start_time', 'time');
            $table->dropColumn(['end_date', 'end_time']);
        });
    }
};
