<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        DB::statement("ALTER TABLE monthly_settlements MODIFY COLUMN status ENUM('draft', 'submitted', 'confirmed', 'approved', 'rejected') DEFAULT 'draft'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE monthly_settlements MODIFY COLUMN status ENUM('draft', 'submitted', 'approved', 'returned') DEFAULT 'draft'");
    }
};
