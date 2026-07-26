<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // الخطوة 1: إضافة العمود nullable أولاً
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id')->nullable()->after('center_id');
            $table->foreign('program_id')->references('id')->on('programs')->restrictOnDelete();
        });

        // الخطوة 2: تعيين البرنامج الأكاديمي لجميع الطلاب الحاليين
        $academicId = DB::table('programs')->where('code', 'academic')->value('id');

        if ($academicId) {
            DB::table('students')->whereNull('program_id')->update(['program_id' => $academicId]);
        }

        // الخطوة 3: جعل العمود NOT NULL بعد تحديث البيانات
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropColumn('program_id');
        });
    }
};
