<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // حالة طلب التخرج: null = لم يتقدم، pending = منتظر المراجعة، approved = موافق، rejected = مرفوض
            $table->enum('graduation_request_status', ['pending', 'approved', 'rejected'])->nullable()->after('is_graduate');
            // المسمى الوظيفي
            $table->string('job_title')->nullable()->after('graduation_request_status');
            // ملاحظات المدير عند الرفض
            $table->text('graduation_rejection_reason')->nullable()->after('job_title');
            // تاريخ تقديم الطلب
            $table->timestamp('graduation_requested_at')->nullable()->after('graduation_rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'graduation_request_status',
                'job_title',
                'graduation_rejection_reason',
                'graduation_requested_at',
            ]);
        });
    }
};
