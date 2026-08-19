<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tablesNeedingSoftDeletes = [
            'violations',
            'penalties',
            'commitments',
            'leaves',
            'absences',
            'activities',
            'activity_participants',
            'news',
            'vouchers',
            'monthly_budgets',
            'monthly_settlements',
            'food_distributions',
            'food_monthly_settlements',
            'circle_attendances',
            'student_grades',
            'student_achievements',
            'vehicle_violations',
            'funds',
            'clubs',
            'club_members',
        ];

        foreach ($tablesNeedingSoftDeletes as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes()->nullable()->after('updated_at');
                });
            }
        }

        if (Schema::hasTable('annual_archives') && !Schema::hasColumn('annual_archives', 'archived_files')) {
            Schema::table('annual_archives', function (Blueprint $table) {
                $table->json('archived_files')->nullable()->after('data');
            });
        }
    }

    public function down(): void
    {
        $tablesNeedingSoftDeletes = [
            'violations',
            'penalties',
            'commitments',
            'leaves',
            'absences',
            'activities',
            'activity_participants',
            'news',
            'vouchers',
            'monthly_budgets',
            'monthly_settlements',
            'food_distributions',
            'food_monthly_settlements',
            'circle_attendances',
            'student_grades',
            'student_achievements',
            'vehicle_violations',
            'funds',
            'clubs',
            'club_members',
        ];

        foreach ($tablesNeedingSoftDeletes as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }

        if (Schema::hasTable('annual_archives') && Schema::hasColumn('annual_archives', 'archived_files')) {
            Schema::table('annual_archives', function (Blueprint $table) {
                $table->dropColumn('archived_files');
            });
        }
    }
};
