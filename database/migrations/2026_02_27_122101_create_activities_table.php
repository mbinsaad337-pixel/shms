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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->constrained('centers')->onDelete('cascade');
            $table->foreignId('club_id')->nullable()->constrained('clubs')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('activity_date');
            $table->time('time')->nullable();
            $table->string('location')->nullable();
            $table->string('target_audience')->nullable();
            $table->integer('max_participants')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->foreignId('fund_id')->nullable()->constrained('funds')->onDelete('set null');
            $table->enum('status', ['planned', 'published', 'completed', 'cancelled'])->default('planned');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
