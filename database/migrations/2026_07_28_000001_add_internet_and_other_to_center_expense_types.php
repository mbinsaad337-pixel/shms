<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE center_expenses MODIFY type ENUM('rent', 'water', 'electricity', 'internet', 'other') NOT NULL");
    }

    public function down(): void
    {
        DB::table('center_expenses')
            ->whereIn('type', ['internet', 'other'])
            ->update(['type' => 'rent']);

        DB::statement("ALTER TABLE center_expenses MODIFY type ENUM('rent', 'water', 'electricity') NOT NULL");
    }
};
