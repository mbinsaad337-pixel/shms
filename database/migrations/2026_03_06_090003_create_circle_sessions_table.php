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
        Schema::create('circle_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circle_id')->constrained('quran_circles')->onDelete('cascade');
            $table->date('session_date');
            $table->string('title')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circle_sessions');
    }
};
