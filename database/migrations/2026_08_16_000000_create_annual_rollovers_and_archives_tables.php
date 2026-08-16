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
        Schema::create('annual_rollovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->nullable()->constrained('centers')->onDelete('cascade');
            $table->string('year');
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->json('modules');
            $table->json('summary')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('annual_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rollover_id')->constrained('annual_rollovers')->onDelete('cascade');
            $table->foreignId('center_id')->nullable()->constrained('centers')->onDelete('cascade');
            $table->string('year');
            $table->string('module');
            $table->string('sub_type')->nullable();
            $table->string('title');
            $table->unsignedBigInteger('record_id')->nullable();
            $table->timestamp('record_date')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name')->nullable();
            $table->longText('data')->nullable();
            $table->timestamps();

            $table->index(['year', 'module']);
            $table->index('center_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_archives');
        Schema::dropIfExists('annual_rollovers');
    }
};
