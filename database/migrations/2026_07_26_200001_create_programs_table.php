<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();

            // قواعد العمل — Configuration-Driven
            $table->boolean('allows_activities')->default(true);
            $table->boolean('allows_attendance')->default(true);
            $table->boolean('allows_violations')->default(true);
            $table->boolean('allows_evaluation')->default(true);
            $table->boolean('allows_quran_circle')->default(true);
            $table->boolean('allows_leaves')->default(true);
            $table->boolean('requires_academic_data')->default(true);

            $table->enum('nutrition_policy', ['mandatory', 'optional', 'none'])->default('optional');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // إدراج البرنامجين الأساسيين
        DB::table('programs')->insert([
            [
                'name'                 => 'أكاديمي',
                'code'                 => 'academic',
                'description'          => 'الطلاب الأكاديميون المقيمون في المركز والخاضعون لجميع الأنظمة التربوية.',
                'allows_activities'    => true,
                'allows_attendance'    => true,
                'allows_violations'    => true,
                'allows_evaluation'    => true,
                'allows_quran_circle'  => true,
                'allows_leaves'        => true,
                'requires_academic_data' => true,
                'nutrition_policy'     => 'optional',
                'is_active'            => true,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'name'                 => 'تعاوني',
                'code'                 => 'cooperative',
                'description'          => 'النزلاء التعاونيون — يستفيدون من خدمات السكن فقط دون الخضوع للأنظمة التربوية.',
                'allows_activities'    => false,
                'allows_attendance'    => false,
                'allows_violations'    => false,
                'allows_evaluation'    => false,
                'allows_quran_circle'  => false,
                'allows_leaves'        => true,
                'requires_academic_data' => false,
                'nutrition_policy'     => 'optional',
                'is_active'            => true,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
