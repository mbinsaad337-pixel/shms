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
        // Update news table for video support
        Schema::table('news', function (Blueprint $table) {
            $table->string('video_url')->nullable()->after('body');
            $table->string('video_path')->nullable()->after('video_url');
        });

        // Create News Likes table
        Schema::create('news_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unique(['news_id', 'user_id']);
            $table->timestamps();
        });

        // Create News Comments table
        Schema::create('news_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_comments');
        Schema::dropIfExists('news_likes');
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'video_path']);
        });
    }
};
