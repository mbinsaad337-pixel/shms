<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Add time to departure_date (we add a separate time column)
            $table->time('departure_time')->nullable()->after('departure_date');
            $table->time('expected_return_time')->nullable()->after('expected_return_date');
            
            // Add lateness as a new type (modify enum)
            \DB::statement("ALTER TABLE leaves MODIFY COLUMN type ENUM('temporary','vacation','medical','lateness') NOT NULL");
            
            // Rejection reason
            $table->text('rejection_reason')->nullable()->after('approved_by');
            
            // Track if it was converted to violation
            $table->boolean('converted_to_violation')->default(false)->after('rejection_reason');
            $table->foreignId('violation_id')->nullable()->constrained('violations')->onDelete('set null')->after('converted_to_violation');
            
            // Student-submitted leave (pending supervisor approval)
            $table->boolean('submitted_by_student')->default(false)->after('student_id');
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['violation_id']);
            $table->dropColumn(['departure_time', 'expected_return_time', 'rejection_reason', 'converted_to_violation', 'violation_id', 'submitted_by_student']);
            \DB::statement("ALTER TABLE leaves MODIFY COLUMN type ENUM('temporary','vacation','medical') NOT NULL");
        });
    }
};
