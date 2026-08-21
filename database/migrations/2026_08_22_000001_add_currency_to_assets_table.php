<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('currency', 3)->default('YER')->after('value');
        });

        \App\Models\SystemSetting::firstOrCreate(
            ['key' => 'default_currency'],
            ['value' => 'YER', 'label' => 'العملة الافتراضية للنظام', 'group' => 'finance']
        );
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        \App\Models\SystemSetting::where('key', 'default_currency')->delete();
    }
};
