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
        Schema::table('news', function (Blueprint $table) {
            // Drop foreign key if it exists, make column nullable, recreate foreign key
            $table->dropForeign(['center_id']);
            $table->unsignedBigInteger('center_id')->nullable()->change();
            $table->foreign('center_id')->references('id')->on('centers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['center_id']);
            $table->unsignedBigInteger('center_id')->nullable(false)->change();
            $table->foreign('center_id')->references('id')->on('centers')->cascadeOnDelete();
        });
    }
};

