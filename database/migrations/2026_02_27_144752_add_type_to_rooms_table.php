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
        Schema::table('rooms', function (Blueprint $table) {
            $table->enum('type', ['residential', 'study_hall', 'activity_hall', 'other'])->default('residential')->after('center_id');
            $table->integer('capacity_limit')->default(0)->after('capacity'); // In case we want to show a limit vs actual capacity
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['type', 'capacity_limit']);
        });
    }
};
