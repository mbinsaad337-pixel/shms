<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sender_center_id')->nullable()->constrained('centers')->nullOnDelete();
            $table->foreignId('receiver_center_id')->nullable()->constrained('centers')->nullOnDelete();
            $table->string('subject');
            $table->text('body');
            $table->string('attachment')->nullable(); // PDF or image path
            $table->string('attachment_type')->nullable(); // 'pdf' or 'image'
            $table->enum('status', ['unread', 'read', 'replied'])->default('unread');
            $table->enum('priority', ['normal', 'urgent'])->default('normal');
            $table->foreignId('parent_id')->nullable()->constrained('complaints')->nullOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
