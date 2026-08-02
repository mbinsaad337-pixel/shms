<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('commitments', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('requires_guardian_signature');
            $table->string('title')->nullable()->after('text');
        });
    }

    public function down(): void
    {
        Schema::table('commitments', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'title']);
        });
    }
};
