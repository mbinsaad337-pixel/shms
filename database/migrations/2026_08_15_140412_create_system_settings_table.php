<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        DB::table('system_settings')->insert([
            [
                'key'        => 'dept_manager_phone',
                'value'      => null,
                'label'      => 'رقم هاتف مدير قسم المراكز الطلابية (واتساب)',
                'group'      => 'notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'dept_manager_name',
                'value'      => 'مدير القسم',
                'label'      => 'اسم مدير قسم المراكز الطلابية',
                'group'      => 'notifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'        => 'overspend_approval_required',
                'value'      => '1',
                'label'      => 'تفعيل طلب الموافقة عند تجاوز رصيد الصندوق',
                'group'      => 'finance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
