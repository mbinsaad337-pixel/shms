@extends('layouts.app')

@section('title', 'تفاصيل السجل المؤرشف - ' . $archive->title)

@php
    $keysMap = [
        'id' => 'معرف السجل الأصلي',
        'center_id' => 'المركز',
        'student_id' => 'الطالب',
        'user_id' => 'المستخدم',
        'fund_id' => 'الصندوق',
        'vehicle_id' => 'المركبة',
        'room_id' => 'الغرفة',
        'program_id' => 'البرنامج',
        'club_id' => 'النادي',
        'circle_id' => 'الحلقة',
        'session_id' => 'الجلسة',
        'activity_id' => 'النشاط',
        'violation_id' => 'المخالفة المرتبطة',
        'subscription_id' => 'الاشتراك',
        'budget_id' => 'الموازنة',
        'target_fund_id' => 'الصندوق المستهدف',
        'rollover_id' => 'عملية الترحيل',
        'recorded_by' => 'المسجّل بواسطة',
        'created_by' => 'أنشئ بواسطة',
        'approved_by' => 'اعتمد بواسطة',
        'submitted_by' => 'قدّم بواسطة',
        'applied_by' => 'طبّق بواسطة',
        'distributed_by' => 'وزّع بواسطة',
        'performed_by' => 'نفّذ بواسطة',
        'name' => 'الاسم',
        'name_ar' => 'الاسم بالعربية',
        'name_en' => 'الاسم بالإنجليزية',
        'student_name' => 'اسم الطالب',
        'title' => 'العنوان / الموضوع',
        'subject' => 'الموضوع / المادة',
        'description' => 'البيان والتفاصيل',
        'body' => 'نص التقرير / الرسالة',
        'text' => 'النص',
        'notes' => 'الملاحظات والتعليمات',
        'amount' => 'المبلغ (ريال)',
        'total_amount' => 'المبلغ الإجمالي (ريال)',
        'grand_total' => 'الإجمالي الكلي (ريال)',
        'total_cost' => 'التكلفة الإجمالية (ريال)',
        'cost' => 'التكلفة (ريال)',
        'total_revenue' => 'إجمالي الإيرادات (ريال)',
        'total_expenses' => 'إجمالي المصروفات (ريال)',
        'total_debt' => 'إجمالي الديون (ريال)',
        'net_result' => 'النتيجة الصافية (ريال)',
        'total_spent' => 'إجمالي المصروف (ريال)',
        'total_remaining' => 'المتبقي (ريال)',
        'budget' => 'الموازنة المعتمدة (ريال)',
        'fine_amount' => 'مبلغ الغرامة (ريال)',
        'total_income' => 'إجمالي الإيرادات (ريال)',
        'balance' => 'الرصيد المتبقي (ريال)',
        'currency' => 'العملة',
        'month' => 'الشهر',
        'year' => 'السنة',
        'month_year' => 'الشهر/السنة',
        'status' => 'الحالة',
        'type' => 'النوع',
        'sub_type' => 'النوع الفرعي',
        'category' => 'التصنيف',
        'severity' => 'درجة الشدة / الخطورة',
        'penalty_type' => 'نوع الجزاء',
        'violation_type' => 'نوع المخالفة',
        'absence_type' => 'نوع الغياب',
        'distribution_type' => 'نوع التوزيع',
        'result_type' => 'نوع النتيجة',
        'semester' => 'الفصل الدراسي',
        'academic_year' => 'السنة الأكاديمية',
        'reason' => 'السبب / المبرر',
        'release_reason' => 'سبب الإخلاء',
        'rejection_reason' => 'سبب الرفض',
        'date' => 'التاريخ',
        'record_date' => 'تاريخ السجل',
        'violation_date' => 'تاريخ المخالفة',
        'departure_date' => 'تاريخ المغادرة',
        'departure_time' => 'وقت المغادرة',
        'expected_return_date' => 'تاريخ العودة المتوقع',
        'expected_return_time' => 'وقت العودة المتوقع',
        'actual_return_date' => 'تاريخ العودة الفعلي',
        'created_at' => 'تاريخ الإنشاء الأصلي',
        'updated_at' => 'تاريخ التحديث الأصلي',
        'deleted_at' => 'تاريخ الحذف',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'start_time' => 'وقت البداية',
        'end_time' => 'وقت النهاية',
        'assigned_at' => 'تاريخ التسكين',
        'released_at' => 'تاريخ الإخلاء',
        'published_at' => 'تاريخ النشر',
        'resolved_at' => 'تاريخ المعالجة والحل',
        'registered_at' => 'تاريخ التسجيل',
        'submitted_at' => 'تاريخ التقديم',
        'approved_at' => 'تاريخ الاعتماد',
        'distributed_at' => 'تاريخ التوزيع',
        'joined_at' => 'تاريخ الانضمام',
        'session_date' => 'تاريخ الجلسة',
        'achievement_date' => 'تاريخ الإنجاز',
        'payee_or_payer' => 'الطرف المستفيد / الدافع',
        'payment_method' => 'طريقة الدفع / الصرف',
        'voucher_number' => 'رقم السند',
        'voucher_type' => 'نوع السند',
        'invoice_number' => 'رقم الفاتورة',
        'vendor_name' => 'اسم المورد / الشركة',
        'plate_number' => 'رقم اللوحة للمركبة',
        'meal_type' => 'نوع الوجبة الغذائية',
        'dish_number' => 'رقم الصحن',
        'scan_type' => 'نوع المسح',
        'qr_group_id' => 'مجموعة QR',
        'student_qr_group_id' => 'مجموعة الطالب QR',
        'group_name' => 'اسم المجموعة',
        'group_members_count' => 'عدد أعضاء المجموعة',
        'building' => 'المبنى',
        'apartment' => 'الشقة',
        'floor' => 'الطابق',
        'room_number' => 'رقم الغرفة',
        'sura' => 'السورة القرآنية',
        'verse' => 'الآية',
        'verse_from' => 'من الآية',
        'verse_to' => 'إلى الآية',
        'topic' => 'الموضوع / الدرس',
        'grade' => 'الدرجة المحصلة',
        'gpa_percentage' => 'نسبة المعدل',
        'max_grade' => 'الدرجة الكبرى',
        'score' => 'النقاط / التقييم',
        'priority' => 'الأولوية',
        'response' => 'الرد / الإجراء المتخذ',
        'is_handled' => 'تمت المعالجة',
        'is_justified' => 'الغياب مبرر',
        'is_graduate' => 'خريج',
        'is_system' => 'صندوق نظامي',
        'is_active' => 'نشط',
        'is_published' => 'منشور',
        'has_excuse' => 'له مبرر',
        'submitted_by_student' => 'قدّمه الطالب',
        'converted_to_violation' => 'حُوّل لمخالفة',
        'requires_guardian_signature' => 'يتطلب توقيع ولي الأمر',
        'attended' => 'حاضر',
        'count' => 'العدد / الكمية',
        'quantity' => 'الكمية',
        'attended_count' => 'عدد الحاضرين',
        'absent_count' => 'عدد الغائبين',
        'max_participants' => 'أقصى عدد مشاركين',
        'participants' => 'المشاركون',
        'items' => 'البنود والتفاصيل',
        'lines' => 'بنود الموازنة',
        'attendance' => 'الحضور',
        'details' => 'التفاصيل الفرعية',
        'gallery' => 'معرض الصور',
        'members' => 'الأعضاء',
        'location' => 'المكان / الموقع',
        'target_audience' => 'الفئة المستهدفة',
        'views_count' => 'عدد المشاهدات',
        'attachment' => 'المرفق',
        'attachment_pdf' => 'مرفق PDF',
        'attachments' => 'المرفقات',
        'cover_image' => 'صورة الغلاف',
        'video_path' => 'الفيديو',
        'video_url' => 'رابط الفيديو',
        'image_path' => 'الصورة',
        'photo' => 'الصورة الشخصية',
        'receipt' => 'الإيصال',
        'logo' => 'الشعار',
        'file_path' => 'الملف',
        'certificate_file' => 'ملف الشهادة',
        'document_photo' => 'صورة المستند',
        'id_card_file' => 'ملف بطاقة الهوية',
        'certificate_file' => 'ملف الشهادة',
        'university_card_file' => 'ملف بطاقة الجامعة',
        'attachment_type' => 'نوع المرفق',
        'receipt_type' => 'نوع الإيصال',
        'avatar' => 'الصورة الرمزية',
        'student_number' => 'رقم الطالب',
        'national_id' => 'رقم الهوية',
        'surname' => 'اللقب',
        'major' => 'التخصص',
        'level' => 'المستوى',
        'phone' => 'الهاتف',
        'email' => 'البريد الإلكتروني',
        'is_graduated' => 'متخرج',
        'gpa' => 'المعدل',
        'academic_year' => 'السنة الدراسية',
        'total_remaining' => 'المتبقي',
        'marital_status' => 'الحالة الاجتماعية',
        'blood_type'=>'فصيلة الدم',
        'university' => 'الجامعة',
        'university_id' => 'رقم بطاقة الجامعة',
        'college' => 'الكلية',
        'academic_level' => 'المستوى الأكاديمي',
        'permanent_address' => 'العنوان الدائم',
        'emergency_name' => 'اسم جهة الطوارئ',
        'emergency_phone' => 'هاتف جهة الطوارئ',
        'emergency_relation' => 'صلة القرابة مع جهة الطوارئ',
        'program_name' => 'نظام التسكين',

        'place_of_birth' => 'مكان الميلاد',
        'id_card_number' => 'رقم بطاقة الهوية',
        'id_card_source' => 'جهة إصدار البطاقة',
        'id_card_date' => 'تاريخ إصدار البطاقة',
        'nationality' => 'الجنسية',
        'health_status' => 'الحالة الصحية',
        'city' => 'المدينة',
        'dependents_count' => 'عدد المعالين',
        'governorate' => 'المحافظة',
        'district' => 'المديرية',
        'village' => 'القرية',
        'home_phone' => 'الهاتف المنزلي',
        'last_certificate' => 'آخر شهادة حصل عليها',
        'last_cert_major' => 'تخصص آخر شهادة',
        'last_cert_grade' => 'درجة آخر شهادة',
        'graduation_year' => 'سنة التخرج',
        'graduated_school' => 'مدرسة التخرج',
        'enrollment_date' => 'تاريخ الالتحاق',
        'study_duration' => 'مدة الدراسة',
        'remaining_period' => 'الفترة المتبقية',
        'expected_graduation' => 'تاريخ التخرج المتوقع',
        'current_academic_year' => 'السنة الأكاديمية الحالية',
        'skills' => 'المهارات',
        'guardian_name' => 'اسم ولي الأمر',
        'guardian_relation' => 'صلة القرابة مع ولي الأمر',
        'guardian_education' => 'تعليم ولي الأمر',
        'guardian_phone' => 'هاتف ولي الأمر',
        'guardian_job' => 'وظيفة ولي الأمر',
        'family_males' => 'ذكور الأسرة',
        'family_females' => 'إناث الأسرة',
        'family_avg_income' => 'متوسط دخل الأسرة',
        'family_workers' => 'عمال الأسرة',
        'barcode' => 'الباركود',
        'annual_fees' => 'الرسوم السنوية',
        'registration_date' => 'تاريخ التسجيل',
        'is_profile_approved' => 'الملف معتمد',
        'can_edit_profile' => 'يمكنه تعديل الملف',
        'profile_step' => 'خطوة الملف الشخصي',
        'profile_completion' => 'اكتمال الملف الشخصي',
        'graduation_request_status' => 'حالة طلب التخرج',
        'job_title' => 'المسمى الوظيفي',
        'graduation_rejection_reason' => 'سبب رفض التخرج',
        'graduation_requested_at' => 'تاريخ طلب التخرج',
        'room_number' => 'رقم الغرفة',




        'birth_certificate_number' => 'رقم شهادة الميلاد',
        'birth_certificate_file' => 'ملف شهادة الميلاد',
        'birth_certificate_date' => 'تاريخ شهادة الميلاد',
        'birth_certificate_place' => 'مكان شهادة الميلاد',
        'academic_year' => 'السنة الدراسية',
        'total_remaining' => 'المتبقي',
        'result_type' => 'نوع النتيجة',
        'place_of_birth' => 'مكان الميلاد',
        
        'nationality' => 'الجنسية',
        'birth_certificate_number' => 'رقم شهادة الميلاد',
        'birth_certificate_file' => 'ملف شهادة الميلاد',
        'birth_certificate_date' => 'تاريخ شهادة الميلاد',
        'birth_certificate_place' => 'مكان شهادة الميلاد',
        'birth_certificate_issued_by' => 'صادر عن شهادة الميلاد',
        'birth_certificate_issued_date' => 'تاريخ إصدار شهادة الميلاد',
        'is_graduated' => 'متخرج',
        'gpa' => 'المعدل',
        'academic_year' => 'السنة الدراسية',
        'total_remaining' => 'المتبقي',
        'result_type' => 'نوع النتيجة',
        'id_card_number'=>'رقم بطاقة الهوية',
        'date_of_birth'=>'تاريخ الميلاد',
        'gender'=>'الجنس',
    ];

    $hiddenFields = [
        'pivot', 'id', 'center_id', 'student_id', 'rollover_id',
        'created_at', 'updated_at', 'deleted_at',
    ];

    $idLabels = [
        'center_id' => 'المركز',
        'student_id' => 'الطالب',
        'fund_id' => 'الصندوق',
        'vehicle_id' => 'المركبة',
        'room_id' => 'الغرفة',
        'program_id' => 'نظام التسكين',
        'club_id' => 'النادي',
        'circle_id' => 'الحلقة',
        'session_id' => 'الجلسة',
        'activity_id' => 'النشاط',
        'violation_id' => 'المخالفة المرتبطة',
        'subscription_id' => 'الاشتراك',
        'budget_id' => 'الموازنة',
        'target_fund_id' => 'الصندوق المستهدف',
        'recorded_by' => 'المسجّل بواسطة',
        'created_by' => 'أنشئ بواسطة',
        'approved_by' => 'اعتمد بواسطة',
        'submitted_by' => 'قدّم بواسطة',
        'applied_by' => 'طبّق بواسطة',
        'distributed_by' => 'وزّع بواسطة',
        'performed_by' => 'نفّذ بواسطة',
        'user_id' => 'المستخدم',
    ];

    $moduleMeta = [
        'administrative' => [
            'name' => 'الإجراءات الإدارية والجزاءات',
            'icon' => 'fas fa-shield-alt',
            'badge' => 'bg-amber-100 text-amber-800 border-amber-200',
            'gradient' => 'from-amber-600 to-amber-800',
        ],
        'activities' => [
            'name' => 'الأنشطة والأخبار والفعاليات',
            'icon' => 'fas fa-calendar-check',
            'badge' => 'bg-blue-100 text-blue-800 border-blue-200',
            'gradient' => 'from-blue-600 to-blue-800',
        ],
        'financial' => [
            'name' => 'النظام المالي والسندات',
            'icon' => 'fas fa-file-invoice-dollar',
            'badge' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'gradient' => 'from-emerald-700 to-teal-800',
        ],
        'nutrition' => [
            'name' => 'نظام التغذية والوجبات',
            'icon' => 'fas fa-utensils',
            'badge' => 'bg-purple-100 text-purple-800 border-purple-200',
            'gradient' => 'from-purple-700 to-purple-900',
        ],
        'quran' => [
            'name' => 'الحلقات القرآنية والحضور',
            'icon' => 'fas fa-quran',
            'badge' => 'bg-teal-100 text-teal-800 border-teal-200',
            'gradient' => 'from-teal-600 to-emerald-800',
        ],
        'academic' => [
            'name' => 'الأكاديمي والدرجات والإنجازات',
            'icon' => 'fas fa-graduation-cap',
            'badge' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'gradient' => 'from-indigo-600 to-indigo-800',
        ],
        'rooms' => [
            'name' => 'تسكين وإخلاء الغرف',
            'icon' => 'fas fa-bed',
            'badge' => 'bg-rose-100 text-rose-800 border-rose-200',
            'gradient' => 'from-rose-600 to-rose-800',
        ],
        'vehicles' => [
            'name' => 'مركبات الطلاب ومخالفاتها',
            'icon' => 'fas fa-car',
            'badge' => 'bg-orange-100 text-orange-800 border-orange-200',
            'gradient' => 'from-orange-600 to-orange-800',
        ],
        'complaints' => [
            'name' => 'الشكاوى والاقتراحات والإشعارات',
            'icon' => 'fas fa-comments',
            'badge' => 'bg-sky-100 text-sky-800 border-sky-200',
            'gradient' => 'from-sky-600 to-cyan-800',
        ],
        'graduates' => [
            'name' => 'الطلاب الخريجون (أرشيف التخرج)',
            'icon' => 'fas fa-user-graduate',
            'badge' => 'bg-teal-100 text-teal-800 border-teal-200',
            'gradient' => 'from-teal-700 to-emerald-900',
        ],
    ];

    $meta = $moduleMeta[$archive->module] ?? [
        'name' => $archive->module,
        'icon' => 'fas fa-archive',
        'badge' => 'bg-gray-100 text-gray-800 border-gray-200',
        'gradient' => 'from-navy via-[#00335e] to-navy',
    ];

    $data = (array) $archive->data;

    // Separate scalar fields from complex array/subtable fields
    $scalarData = [];
    $complexData = [];
    foreach ($data as $key => $val) {
        if (is_array($val)) {
            $complexData[$key] = $val;
        } else {
            $scalarData[$key] = $val;
        }
    }
@endphp

@section('content')
    <div class="space-y-6 pb-16 font-cairo">

        <!-- Top Action & Navigation Bar (Hidden on Print) -->
        <div
            class="print:hidden flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs font-bold text-gray-500 font-almarai">
                <a href="{{ route('dashboard') }}" class="hover:text-navy transition-colors">الرئيسية</a>
                <i class="fas fa-chevron-left text-[10px] text-gray-300"></i>
                <a href="{{ route('annual-rollover.index') }}" class="hover:text-navy transition-colors">الترحيل السنوي وأرشيف
                    السنوات</a>
                <i class="fas fa-chevron-left text-[10px] text-gray-300"></i>
                <span class="text-navy font-black">تفاصيل السجل المؤرشف #{{ $archive->id }}</span>
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <a href="{{ route('annual-rollover.export-archive-pdf', $archive) }}" target="_blank"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold font-cairo flex items-center gap-2 transition-all shadow-sm">
                    <i class="fas fa-file-pdf"></i>
                    <span>طباعة السجل</span>
                </a>
                <a href="{{ route('annual-rollover.index') }}"
                    class="px-4 py-2 bg-navy/10 hover:bg-navy hover:text-white text-navy rounded-xl text-xs font-bold font-cairo flex items-center gap-2 transition-all shadow-sm">
                    <i class="fas fa-arrow-right"></i>
                    <span>العودة للأرشيف</span>
                </a>
                <button onclick="window.close()"
                    class="px-3 py-2 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 rounded-xl text-xs font-bold font-cairo flex items-center gap-1.5 transition-all">
                    <i class="fas fa-times"></i>
                    <span>إغلاق التاب</span>
                </button>
            </div>
        </div>

        <!-- Hero Identity Banner -->
        <div
            class="bg-gradient-to-r {{ $meta['gradient'] }} rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -left-10 -bottom-10 opacity-10 pointer-events-none">
                <i class="{{ $meta['icon'] }} text-9xl text-white"></i>
            </div>

            <div class="relative z-10 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-gold text-xl font-bold border border-white/20 shadow-inner">
                            <i class="{{ $meta['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="px-3 py-0.5 rounded-full text-xs font-black bg-white/20 text-navy backdrop-blur-sm">
                                    {{ $meta['name'] }}
                                </span>
                                <span class="px-3 py-0.5 rounded-full text-xs font-black bg-gold/90 text-navy">
                                    سنة {{ $archive->year }}
                                </span>
                                @if ($archive->sub_type)
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/10 text-gray-400">
                                        {{ $archive->sub_type }}
                                    </span>
                                @endif
                            </div>
                            <h1 class="text-xl md:text-2xl font-black text-gold mt-1.5">
                                {{ $archive->title }}
                            </h1>
                        </div>
                    </div>

                    <div class="text-left bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-white/10">
                        <span class="text-[10px] text-gray-400 block font-almarai">معرف الأرشيف المرجعي</span>
                        <span
                            class="text-sm font-black font-mono text-gold">ARC-{{ str_pad($archive->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>

                <div
                    class="pt-3 border-t border-white/15 flex flex-wrap items-center gap-4 text-xs font-almarai text-gray-400">
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-building text-gold/80"></i>
                        <span>{{ optional($archive->center)->name ?? 'المركز العام' }}</span>
                    </span>
                    <span class="text-white/40">•</span>
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-calendar-alt text-gold/80"></i>
                        <span>تاريخ السجل الأصلي:
                            {{ $archive->record_date ? $archive->record_date->format('Y/m/d H:i') : '-' }}</span>
                    </span>
                    <span class="text-white/40">•</span>
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-archive text-gold/80"></i>
                        <span>تاريخ الأرشفة: {{ $archive->created_at->format('Y/m/d H:i') }}</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Overview Stats Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 font-almarai">

            <!-- Year -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3.5">
                <div
                    class="w-11 h-11 rounded-xl bg-navy/10 text-navy flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fas fa-calendar"></i>
                </div>
                <div>
                    <span class="text-[11px] text-gray-400 font-bold block font-cairo">السنة المؤرشفة</span>
                    <span class="text-sm font-black text-navy font-cairo">{{ $archive->year }}</span>
                </div>
            </div>

            <!-- Student -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3.5">
                <div
                    class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="overflow-hidden">
                    <span class="text-[11px] text-gray-400 font-bold block font-cairo">الطالب المعني</span>
                    <span class="text-xs font-bold text-gray-800 truncate block">
                        {{ $archive->student_name ?: (optional($archive->student)->name_ar ?: 'غير محدد / سجل عام') }}
                    </span>
                </div>
            </div>

            <!-- Amount -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3.5">
                <div
                    class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <span class="text-[11px] text-gray-400 font-bold block font-cairo">المبلغ المالي</span>
                    <span class="text-xs font-black text-emerald-700 font-cairo">
                        {{ $archive->amount > 0 ? number_format($archive->amount, 2) . ' ريال' : 'لا يوجد مبلغ' }}
                    </span>
                </div>
            </div>

            <!-- Performed By -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3.5">
                <div
                    class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="overflow-hidden">
                    <span class="text-[11px] text-gray-400 font-bold block font-cairo">المنفذ للترحيل</span>
                    <span class="text-xs font-bold text-gray-800 truncate block">
                        {{ optional(optional($archive->rollover)->user)->name ?? 'النظام' }}
                    </span>
                </div>
            </div>

        </div>

        <!-- Main Content Details Grid -->
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <h2 class="text-base font-black text-navy font-cairo flex items-center gap-2.5">
                    <i class="fas fa-th-list text-gold"></i>
                    <span>بيانات وتفاصيل السجل المؤرشف بالكامل</span>
                </h2>
                <span class="text-xs text-gray-400 font-almarai">
                    عدد الحقول: {{ count($scalarData) }} حقل
                </span>
            </div>

            @if (empty($scalarData))
                <div class="text-center py-10 text-gray-400 font-almarai text-sm">
                    لا توجد حقول أحادية مسجلة في هذا الأرشيف.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 font-almarai">
                    @foreach ($scalarData as $key => $val)
                        @php
                            if (in_array($key, $hiddenFields) && !isset($idLabels[$key])) continue;

                            $label = $idLabels[$key] ?? $keysMap[$key] ?? str_replace('_', ' ', $key);
                            $isIdField = isset($idLabels[$key]);
                            $resolvedValue = null;
                            if ($isIdField && $val) {
                                $resolvedValue = $idMap[$key] ?? null;
                            }
                            $isAmount =
                                str_contains($key, 'amount') ||
                                str_contains($key, 'cost') ||
                                str_contains($key, 'budget') ||
                                str_contains($key, 'fine') ||
                                str_contains($key, 'income') ||
                                str_contains($key, 'expense') ||
                                str_contains($key, 'balance') ||
                                str_contains($key, 'revenue') ||
                                str_contains($key, 'debt') ||
                                str_contains($key, 'remaining') ||
                                str_contains($key, 'spent');
                            $isDate = str_ends_with($key, '_at') || str_ends_with($key, '_date') || $key === 'date' || $key === 'month' || $key === 'year';
                            $isLong = is_string($val) && mb_strlen($val) > 80;
                            $isHiddenInternal = in_array($key, ['id', 'center_id', 'student_id', 'rollover_id']);
                        @endphp

                        @if ($isIdField && !$resolvedValue && $isHiddenInternal && empty($val))
                            @continue
                        @endif

                        @if ($isIdField && $resolvedValue && in_array($key, ['center_id', 'student_id']))
                            @php
                                $label = $idLabels[$key] ?? $label;
                            @endphp
                        @endif

                        <div
                            class="{{ $isLong ? 'md:col-span-2 lg:col-span-3' : '' }} bg-gray-50/70 hover:bg-blue-50/20 p-4 rounded-2xl border border-gray-100 transition-all flex flex-col justify-between">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-[11px] font-bold text-gray-500 font-cairo flex items-center gap-1.5">
                                    <i class="fas fa-circle text-[6px] text-gold"></i>
                                    {{ $label }}
                                </span>
                                @if ($isIdField)
                                    <span class="text-[9px] font-mono text-blue-400 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                                        ID: {{ $val }}
                                    </span>
                                @endif
                            </div>

                            <div class="text-xs font-bold text-navy break-words leading-relaxed font-almarai">
                                @if ($val === null || $val === '' || (is_string($val) && trim($val) === ''))
                                    <span class="text-gray-300 font-normal">--</span>
                                @elseif(is_bool($val))
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-cairo {{ $val ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $val ? 'نعم' : 'لا' }}
                                    </span>
                                @elseif($isIdField && $resolvedValue)
                                    <span class="px-3 py-1 rounded-xl text-xs font-bold bg-blue-50 text-blue-700 font-cairo flex items-center gap-1.5">
                                        <i class="fas fa-link text-[9px]"></i>
                                        {{ $resolvedValue }}
                                    </span>
                                @elseif($isIdField && !$resolvedValue && $val)
                                    <span class="px-3 py-1 rounded-xl text-xs font-bold bg-gray-100 text-gray-500 font-cairo">
                                        #{{ $val }}
                                    </span>
                                @elseif($isAmount && is_numeric($val))
                                    <span class="text-sm font-black text-emerald-700 font-cairo">
                                        {{ number_format((float) $val, 2) }} <span
                                            class="text-[10px] font-normal text-emerald-600">ريال</span>
                                    </span>
                                @elseif($key === 'status')
                                    @php
                                        $statusLabels = [
                                            'approved' => 'معتمد', 'pending' => 'قيد الانتظار', 'rejected' => 'مرفوض',
                                            'active' => 'نشط', 'inactive' => 'غير نشط', 'draft' => 'مسودة',
                                            'submitted' => 'مقدّم', 'returned' => 'عاد', 'not_returned' => 'لم يعد',
                                            'paid' => 'مدفوع', 'unpaid' => 'غير مدفوع',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-xl text-xs font-bold {{ in_array($val, ['approved', 'active', 'paid']) ? 'bg-emerald-100 text-emerald-800' : (in_array($val, ['rejected', 'inactive', 'unpaid']) ? 'bg-rose-100 text-rose-800' : 'bg-navy/10 text-navy') }} font-cairo">
                                        {{ $statusLabels[$val] ?? $val }}
                                    </span>
                                @elseif($key === 'type' || $key === 'penalty_type' || $key === 'violation_type' || $key === 'absence_type' || $key === 'meal_type' || $key === 'distribution_type' || $key === 'result_type' || $key === 'voucher_type' || $key === 'semester')
                                    <span class="px-3 py-1 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 font-cairo">
                                        {{ $val }}
                                    </span>
                                @elseif($key === 'currency')
                                    @php
                                        $currencyLabels = ['YER' => 'ريال يمني', 'SAR' => 'ريال سعودي', 'USD' => 'دولار أمريكي'];
                                    @endphp
                                    <span class="px-3 py-1 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 font-cairo">
                                        {{ $currencyLabels[$val] ?? $val }}
                                    </span>
                                @elseif(in_array($key, ['month', 'month_year']))
                                    <span class="px-3 py-1 rounded-xl text-xs font-bold bg-purple-50 text-purple-700 font-cairo">
                                        {{ $val }}
                                    </span>
                                @elseif(in_array($key, ['sura']))
                                    <span class="px-3 py-1 rounded-xl text-xs font-bold bg-teal-50 text-teal-700 font-cairo">
                                        <i class="fas fa-quran ml-1"></i> {{ $val }}
                                    </span>
                                @elseif(in_array($key, ['payment_method']))
                                    <span class="px-3 py-1 rounded-xl text-xs font-bold bg-gray-100 text-gray-700 font-cairo">
                                        {{ $val }}
                                    </span>
                                @elseif(in_array($key, ['attachment', 'attachment_pdf', 'attachments', 'cover_image', 'image_path', 'photo', 'receipt', 'logo', 'file_path', 'certificate_file', 'document_photo']))
                                    @if ($val && !is_array($val))
                                        <span class="px-3 py-1 rounded-xl text-[10px] font-bold bg-gray-100 text-gray-600 font-mono break-all">
                                            {{ $val }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">--</span>
                                    @endif
                                @elseif($isDate && $val)
                                    @php
                                        $dt = null;
                                        if (is_string($val)) {
                                            $dt = \Carbon\Carbon::parse($val);
                                        } elseif ($val instanceof \Carbon\Carbon) {
                                            $dt = $val;
                                        }
                                    @endphp
                                    @if ($dt)
                                        <span class="px-3 py-1 rounded-xl text-xs font-bold bg-sky-50 text-sky-700 font-cairo flex items-center gap-1.5">
                                            <i class="fas fa-clock text-[9px]"></i>
                                            {{ $dt->format('Y/m/d') }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-xl text-xs font-bold bg-sky-50 text-sky-700 font-cairo">
                                            {{ $val }}
                                        </span>
                                    @endif
                                @elseif(is_array($val))
                                    <span class="px-3 py-1 rounded-xl text-[10px] font-bold bg-gray-100 text-gray-600 font-mono break-all">
                                        {{ implode(', ', array_map(fn($item) => is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE) : $item, $val)) }}
                                    </span>
                                @else
                                    {{ $val }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Complex Data (Arrays / Related Records) -->
        @if (!empty($complexData))
            @foreach ($complexData as $cKey => $cRows)
                @php
                    $cLabel = $keysMap[$cKey] ?? str_replace('_', ' ', $cKey);
                    if (!is_array($cRows)) continue;
                    $isIndexed = !empty($cRows) && array_is_list($cRows);
                    $rows = $isIndexed ? $cRows : [$cRows];
                    if (empty($rows)) continue;
                    $flatRows = array_filter($rows, fn($r) => is_array($r));
                    if (empty($flatRows)) continue;
                    $firstRow = reset($flatRows);
                    $columns = array_keys($firstRow);
                    $skipCols = ['id', 'created_at', 'updated_at', 'deleted_at', 'pivot'];
                    $visibleCols = array_values(array_diff($columns, $skipCols));
                    $colLabels = [
                        'student_id' => 'الطالب', 'name' => 'الاسم', 'name_ar' => 'الاسم',
                        'attended' => 'الحضور', 'status' => 'الحالة', 'role' => 'الدور',
                        'sura' => 'السورة', 'verse' => 'الآية', 'is_handled' => 'تمت المعالجة',
                        'registered_at' => 'تاريخ التسجيل', 'joined_at' => 'تاريخ الانضمام',
                        'student_number' => 'رقم الطالب', 'level' => 'المستوى',
                        'club_id' => 'النادي', 'activity_id' => 'النشاط', 'session_id' => 'الجلسة',
                        'center_id' => 'المركز', 'fund_id' => 'الصندوق',
                    ];

                    $allStudentIds = collect($flatRows)->pluck('student_id')->filter()->unique()->values()->all();
                    $resolvedStudents = [];
                    if (!empty($allStudentIds)) {
                        $resolvedStudents = \App\Models\Student::withoutGlobalScopes()
                            ->whereIn('id', $allStudentIds)
                            ->pluck('name_ar', 'id')
                            ->toArray();
                    }

                    $allClubIds = collect($flatRows)->pluck('club_id')->filter()->unique()->values()->all();
                    $resolvedClubs = [];
                    if (!empty($allClubIds)) {
                        $resolvedClubs = \App\Models\Club::withoutGlobalScopes()
                            ->whereIn('id', $allClubIds)
                            ->pluck('name', 'id')
                            ->toArray();
                    }

                    $allActivityIds = collect($flatRows)->pluck('activity_id')->filter()->unique()->values()->all();
                    $resolvedActivities = [];
                    if (!empty($allActivityIds)) {
                        $resolvedActivities = \App\Models\Activity::withoutGlobalScopes()
                            ->whereIn('id', $allActivityIds)
                            ->pluck('name', 'id')
                            ->toArray();
                    }

                    $allUserIds = collect($flatRows)->pluck('user_id')->filter()->unique()->values()->all();
                    $resolvedUsers = [];
                    if (!empty($allUserIds)) {
                        $resolvedUsers = \App\Models\User::whereIn('id', $allUserIds)
                            ->pluck('name', 'id')
                            ->toArray();
                    }

                    $resolveByField = [
                        'student_id' => $resolvedStudents,
                        'club_id' => $resolvedClubs,
                        'activity_id' => $resolvedActivities,
                        'user_id' => $resolvedUsers,
                    ];
                @endphp
                <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-4">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <h2 class="text-base font-black text-navy font-cairo flex items-center gap-2.5">
                            <i class="fas fa-list-ul text-gold"></i>
                            <span>{{ $cLabel }}</span>
                            <span class="bg-blue-100 text-blue-800 text-[10px] px-2 py-0.5 rounded-full font-bold">
                                {{ count($flatRows) }} سجل
                            </span>
                        </h2>
                    </div>

                    @if (count($flatRows) <= 5)
                        <div class="space-y-3">
                            @foreach ($flatRows as $row)
                                <div class="bg-gray-50/70 p-4 rounded-2xl border border-gray-100">
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        @foreach ($row as $rk => $rv)
                                            @if (in_array($rk, $skipCols)) @continue @endif
                                            <div>
                                                <span class="text-[10px] text-gray-400 font-bold font-cairo block mb-0.5">
                                                    {{ $colLabels[$rk] ?? str_replace('_', ' ', $rk) }}
                                                </span>
                                                <span class="text-xs font-bold text-navy font-almarai">
                                                    @if ($rv === null || $rv === '')
                                                        --
                                                    @elseif(is_bool($rv))
                                                        {{ $rv ? 'نعم' : 'لا' }}
                                                    @elseif($rk === 'attended')
                                                        {{ $rv ? 'حاضر' : 'غائب' }}
                                                    @elseif(str_ends_with($rk, '_id') && isset($resolveByField[$rk][$rv]))
                                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-700">
                                                            {{ $resolveByField[$rk][$rv] }}
                                                        </span>
                                                    @elseif((str_ends_with($rk, '_at') || str_ends_with($rk, '_date') || $rk === 'date') && $rv && is_string($rv))
                                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-sky-50 text-sky-700">
                                                            {{ \Carbon\Carbon::parse($rv)->format('Y/m/d') }}
                                                        </span>
                                                    @elseif($rk === 'status')
                                                        {{ $rv }}
                                                    @else
                                                        {{ $rv }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-right border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-[11px] font-bold text-gray-500 font-cairo border-b border-gray-100">
                                        <th class="p-3">#</th>
                                        @foreach ($visibleCols as $col)
                                            <th class="p-3">{{ $colLabels[$col] ?? str_replace('_', ' ', $col) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-xs font-almarai">
                                    @foreach ($flatRows as $i => $row)
                                        <tr class="hover:bg-blue-50/30 transition-colors">
                                            <td class="p-3 text-gray-400">{{ $i + 1 }}</td>
                                            @foreach ($visibleCols as $col)
                                                <td class="p-3 font-bold text-navy">
                                                    @php $rv = $row[$col] ?? null; @endphp
                                                    @if ($rv === null || $rv === '')
                                                        <span class="text-gray-300">--</span>
                                                    @elseif(is_bool($rv))
                                                        {{ $rv ? 'نعم' : 'لا' }}
                                                    @elseif($col === 'attended')
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $rv ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                            {{ $rv ? 'حاضر' : 'غائب' }}
                                                        </span>
                                                    @elseif(str_ends_with($col, '_id') && isset($resolveByField[$col][$rv]))
                                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-700">
                                                            {{ $resolveByField[$col][$rv] }}
                                                        </span>
                                                    @elseif((str_ends_with($col, '_at') || str_ends_with($col, '_date') || $col === 'date') && $rv && is_string($rv))
                                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-sky-50 text-sky-700">
                                                            {{ \Carbon\Carbon::parse($rv)->format('Y/m/d') }}
                                                        </span>
                                                    @else
                                                        {{ $rv }}
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif

        <!-- Archived Files Section -->
        @if ($archive->archived_files && count($archive->archived_files) > 0)
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <h2 class="text-base font-black text-navy font-cairo flex items-center gap-2.5">
                        <i class="fas fa-paperclip text-gold"></i>
                        <span>الملفات والمرفقات المؤرشفة</span>
                        <span class="bg-emerald-100 text-emerald-800 text-[10px] px-2 py-0.5 rounded-full font-bold">
                            {{ count($archive->archived_files) }} ملف
                        </span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($archive->archived_files as $originalPath => $archivedPath)
                        @php
                            $archivedExists = isset($fileStatuses[$originalPath]['archived_exists']) ? $fileStatuses[$originalPath]['archived_exists'] : \Illuminate\Support\Facades\Storage::disk('public')->exists($archivedPath);
                            $originalExists = isset($fileStatuses[$originalPath]['original_exists']) ? $fileStatuses[$originalPath]['original_exists'] : \Illuminate\Support\Facades\Storage::disk('public')->exists($originalPath);
                            $filename = basename($originalPath);
                            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                            $isPdf = $ext === 'pdf';
                        @endphp
                        <div class="bg-gray-50/70 hover:bg-blue-50/20 p-4 rounded-2xl border border-gray-100 transition-all space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl {{ $isImage ? 'bg-blue-100 text-blue-600' : ($isPdf ? 'bg-red-100 text-red-600' : 'bg-gray-200 text-gray-500') }} flex items-center justify-center shrink-0">
                                        <i class="fas {{ $isImage ? 'fa-image' : ($isPdf ? 'fa-file-pdf' : 'fa-file') }} text-lg"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <span class="text-xs font-bold text-navy font-cairo block truncate" title="{{ $filename }}">{{ $filename }}</span>
                                        <span class="text-[10px] text-gray-400 font-almarai">{{ strtoupper($ext) }}</span>
                                    </div>
                                </div>
                                @if ($archivedExists)
                                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-700 flex items-center gap-1">
                                        <i class="fas fa-check-circle"></i> محفوظ
                                    </span>
                                @else
                                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[9px] font-bold bg-red-100 text-red-700 flex items-center gap-1">
                                        <i class="fas fa-exclamation-triangle"></i> مفقود
                                    </span>
                                @endif
                            </div>

                            @if ($isImage && $archivedExists)
                                <div class="rounded-xl overflow-hidden border border-gray-200 bg-white">
                                    <img src="{{ asset('storage/' . $archivedPath) }}" alt="{{ $filename }}"
                                        class="w-full h-40 object-cover cursor-pointer hover:opacity-90 transition-all"
                                        onclick="window.open('{{ asset('storage/' . $archivedPath) }}', '_blank')">
                                </div>
                            @endif

                            <div class="flex items-center gap-2 text-[10px]">
                                @if ($archivedExists)
                                    <a href="{{ asset('storage/' . $archivedPath) }}" target="_blank"
                                        class="flex-1 px-3 py-2 bg-navy text-gold font-bold text-center rounded-xl hover:bg-gold hover:text-navy transition-all">
                                        <i class="fas fa-external-link-alt ml-1"></i> فتح الملف
                                    </a>
                                @endif
                                @if ($originalExists)
                                    <a href="{{ asset('storage/' . $originalPath) }}" target="_blank"
                                        class="flex-1 px-3 py-2 bg-gray-200 text-gray-700 font-bold text-center rounded-xl hover:bg-gray-300 transition-all">
                                        <i class="fas fa-link ml-1"></i> الأصلي
                                    </a>
                                @endif
                            </div>

                            <div class="text-[9px] text-gray-400 font-mono space-y-0.5">
                                <div class="truncate" title="المسار الأصلي: {{ $originalPath }}">
                                    <i class="fas fa-folder-open ml-1"></i> {{ $originalPath }}
                                </div>
                                <div class="truncate" title="مسار الأرشيف: {{ $archivedPath }}">
                                    <i class="fas fa-archive ml-1"></i> {{ $archivedPath }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Rollover Operational Audit Information -->
        @if ($archive->rollover)
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-4">
                <h3
                    class="text-sm font-black text-navy font-cairo flex items-center gap-2.5 pb-3 border-b border-gray-100">
                    <i class="fas fa-history text-gold"></i>
                    <span>بيانات عملية الترحيل السنوي المرتبطة</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs font-almarai">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 font-bold font-cairo block mb-1">السنة المرحلة:</span>
                        <span class="font-black text-navy text-sm font-cairo">{{ $archive->rollover->year }}</span>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 font-bold font-cairo block mb-1">تاريخ ووقت التنفيذ:</span>
                        <span
                            class="font-bold text-gray-700">{{ $archive->rollover->created_at->format('Y/m/d - H:i:s') }}</span>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 font-bold font-cairo block mb-1">المستخدم المنفذ
                            للعملية:</span>
                        <span
                            class="font-bold text-navy">{{ optional($archive->rollover->user)->name ?? 'النظام' }}</span>
                    </div>
                </div>

                @if ($archive->rollover->notes)
                    <div class="p-4 bg-amber-50/60 rounded-2xl border border-amber-200/50 text-xs font-almarai">
                        <span class="text-amber-800 font-bold font-cairo block mb-1">ملاحظات الترحيل السنوي:</span>
                        <p class="text-amber-900 leading-relaxed">{{ $archive->rollover->notes }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Bottom Print / Close Toolbar (Hidden on print) -->
        <div
            class="print:hidden flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <span class="text-xs text-gray-400 font-almarai">
                تم استعراض تفاصيل السجل المؤرشف بنجاح
            </span>

            <div class="flex items-center gap-2">
                <a href="{{ route('annual-rollover.index') }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl font-cairo transition-all">
                    رجوع للأرشيف
                </a>
                <a href="{{ route('annual-rollover.export-archive-pdf', $archive) }}" target="_blank"
                    class="px-5 py-2.5 bg-red-600 text-white font-bold text-xs rounded-xl font-cairo hover:bg-red-700 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-pdf"></i>
                    <span>طباعة هذا السجل</span>
                </a>
            </div>
        </div>

    </div>

    <!-- Print Stylesheet -->
    <style>
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 12pt;
            }

            aside,
            header,
            nav,
            .print\:hidden {
                display: none !important;
            }

            .shadow-sm,
            .shadow-md,
            .shadow-xl {
                box-shadow: none !important;
            }

            .border {
                border-color: #ddd !important;
            }
        }
    </style>
@endsection
