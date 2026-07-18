<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Personal
            $table->string('surname')->nullable()->after('name_ar');                     // اللقب
            $table->string('place_of_birth')->nullable()->after('date_of_birth');        // مكان الميلاد
            $table->string('id_card_number')->nullable()->after('national_id');          // رقم البطاقة / الجواز
            $table->string('id_card_source')->nullable();                                // مصدر البطاقة
            $table->date('id_card_date')->nullable();                                    // تاريخ البطاقة
            $table->string('city')->nullable();                                          // المدينة
            $table->unsignedTinyInteger('dependents_count')->default(0)->nullable();     // عدد أفراد الأسرة المعالة
            $table->enum('health_status', ['good', 'average', 'weak'])->default('good')->nullable(); // الحالة الصحية

            // Address
            $table->string('governorate')->nullable();                                   // المحافظة
            $table->string('district')->nullable();                                      // المديرية
            $table->string('village')->nullable();                                       // القرية
            $table->string('home_phone')->nullable();                                    // هاتف البيت

            // Education
            $table->string('last_certificate')->nullable();                              // آخر شهادة
            $table->string('last_cert_major')->nullable();                               // تخصص الشهادة
            $table->string('last_cert_grade')->nullable();                               // التقدير
            $table->year('graduation_year')->nullable();                                 // سنة التخرج
            $table->string('graduated_school')->nullable();                              // المدرسة المتخرج منها
            $table->date('enrollment_date')->nullable();                                 // تاريخ الالتحاق بالجامعة
            $table->string('study_duration')->nullable();                                // مدة الدراسة
            $table->string('remaining_period')->nullable();                              // الفترة المتبقية
            $table->date('expected_graduation')->nullable();                             // التاريخ المتوقع للتخرج
            $table->string('current_academic_year')->nullable();                         // السنة الدراسية الحالية
            $table->text('skills')->nullable();                                          // الأعمال والمهارات

            // Guardian
            $table->string('guardian_name')->nullable();                                 // اسم ولي الأمر
            $table->string('guardian_relation')->nullable();                             // صلة القرابة
            $table->string('guardian_education')->nullable();                            // مستواه العلمي
            $table->string('guardian_phone')->nullable();                                // هاتف ولي الأمر
            $table->string('guardian_job')->nullable();                                  // عمله الحالي

            // Family Info
            $table->unsignedTinyInteger('family_males')->default(0)->nullable();         // عدد الذكور
            $table->unsignedTinyInteger('family_females')->default(0)->nullable();       // عدد الإناث
            $table->decimal('family_avg_income', 10, 2)->nullable();                    // متوسط دخل الفرد
            $table->json('family_workers')->nullable();                                  // العاملون من أفراد الأسرة
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'surname', 'place_of_birth', 'id_card_number', 'id_card_source', 'id_card_date',
                'city', 'dependents_count', 'health_status',
                'governorate', 'district', 'village', 'home_phone',
                'last_certificate', 'last_cert_major', 'last_cert_grade', 'graduation_year',
                'graduated_school', 'enrollment_date', 'study_duration', 'remaining_period',
                'expected_graduation', 'current_academic_year', 'skills',
                'guardian_name', 'guardian_relation', 'guardian_education', 'guardian_phone', 'guardian_job',
                'family_males', 'family_females', 'family_avg_income', 'family_workers',
            ]);
        });
    }
};
