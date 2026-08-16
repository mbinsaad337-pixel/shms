@extends('layouts.app')

@section('title', 'تفاصيل السجل المؤرشف - ' . $archive->title)

@php
    $keysMap = [
        'id' => 'معرف السجل الأصلي',
        'title' => 'العنوان / الموضوع',
        'name' => 'الاسم',
        'name_ar' => 'الاسم بالعربية',
        'name_en' => 'الاسم بالإنجليزية',
        'subject' => 'الموضوع / المادة',
        'description' => 'البيان والتفاصيل',
        'body' => 'نص التقرير / الرسالة',
        'notes' => 'الملاحظات والتعليمات',
        'amount' => 'المبلغ (ريال)',
        'total_amount' => 'المبلغ الإجمالي (ريال)',
        'grand_total' => 'الإجمالي الكلي (ريال)',
        'total_cost' => 'التكلفة الإجمالية (ريال)',
        'cost' => 'التكلفة (ريال)',
        'budget' => 'الموازنة المعتمدة (ريال)',
        'fine_amount' => 'مبلغ الغرامة (ريال)',
        'total_income' => 'إجمالي الإيرادات (ريال)',
        'total_expenses' => 'إجمالي المصروفات (ريال)',
        'balance' => 'الرصيد المتبقي (ريال)',
        'month' => 'الشهر',
        'year' => 'السنة',
        'status' => 'الحالة',
        'type' => 'النوع',
        'sub_type' => 'النوع الفرعي',
        'category' => 'التصنيف',
        'severity' => 'درجة الشدة / الخطورة',
        'penalty_type' => 'نوع الجزاء',
        'violation_type' => 'نوع المخالفة',
        'reason' => 'السبب / المبرر',
        'release_reason' => 'سبب الإخلاء',
        'date' => 'التاريخ',
        'record_date' => 'تاريخ السجل',
        'violation_date' => 'تاريخ المخالفة',
        'created_at' => 'تاريخ وساعة الإنشاء الأصلي',
        'updated_at' => 'تاريخ وساعة التحديث الأصلي',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'assigned_at' => 'تاريخ التسكين',
        'released_at' => 'تاريخ الإخلاء',
        'published_at' => 'تاريخ النشر',
        'resolved_at' => 'تاريخ المعالجة والحل',
        'student_id' => 'معرف الطالب',
        'student_name' => 'اسم الطالب',
        'teacher_id' => 'معرف المعلم',
        'teacher_name' => 'اسم المعلم',
        'payee_or_payer' => 'الطرف المستفيد / الدافع',
        'payment_method' => 'طريقة الدفع / الصرف',
        'voucher_number' => 'رقم السند',
        'invoice_number' => 'رقم الفاتورة',
        'vendor_name' => 'اسم المورد / الشركة',
        'plate_number' => 'رقم اللوحة للمركبة',
        'meal_type' => 'نوع الوجبة الغذائية',
        'building' => 'المبنى',
        'apartment' => 'الشقة',
        'floor' => 'الطابق',
        'room_number' => 'رقم الغرفة',
        'room_id' => 'معرف الغرفة',
        'sura' => 'السورة القرآنية',
        'verse' => 'الآية',
        'verse_from' => 'من الآية',
        'verse_to' => 'إلى الآية',
        'topic' => 'الموضوع / الدرس',
        'grade' => 'الدرجة المحصلة',
        'max_grade' => 'الدرجة الكبرى',
        'score' => 'النقاط / التقييم',
        'semester' => 'الفصل الدراسي',
        'priority' => 'الأولوية',
        'response' => 'الرد / الإجراء المتخذ',
        'is_handled' => 'تمت المعالجة',
        'is_justified' => 'الغياب مبرر',
        'count' => 'العدد / الكمية',
        'quantity' => 'الكمية',
        'attended_count' => 'عدد الحاضرين',
        'absent_count' => 'عدد الغائبين',
        'participants' => 'قائمة المشاركين بالنشاط',
        'items' => 'البنود والتفاصيل',
        'lines' => 'بنود الموازنة',
        'attendance' => 'سجلات الحضور والغياب',
        'details' => 'التفاصيل الفرعية',
        'location' => 'المكان / الموقع',
        'views_count' => 'عدد المشاهدات',
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
                            class="text-sm font-black font-mono text-gold">#ARC-{{ str_pad($archive->id, 6, '0', STR_PAD_LEFT) }}</span>
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
                            $label = $keysMap[$key] ?? $key;
                            $isAmount =
                                str_contains($key, 'amount') ||
                                str_contains($key, 'cost') ||
                                str_contains($key, 'budget') ||
                                str_contains($key, 'fine') ||
                                str_contains($key, 'income') ||
                                str_contains($key, 'expense') ||
                                str_contains($key, 'balance');
                            $isLong = is_string($val) && mb_strlen($val) > 80;
                        @endphp

                        <div
                            class="{{ $isLong ? 'md:col-span-2 lg:col-span-3' : '' }} bg-gray-50/70 hover:bg-blue-50/20 p-4 rounded-2xl border border-gray-100 transition-all flex flex-col justify-between">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-[11px] font-bold text-gray-500 font-cairo flex items-center gap-1.5">
                                    <i class="fas fa-circle text-[6px] text-gold"></i>
                                    {{ $label }}
                                </span>
                                <span
                                    class="text-[9px] font-mono text-gray-400 bg-white px-2 py-0.5 rounded-md border border-gray-100">
                                    {{ $key }}
                                </span>
                            </div>

                            <div class="text-xs font-bold text-navy break-words leading-relaxed font-almarai">
                                @if ($val === null || $val === '')
                                    <span class="text-gray-300 font-normal">--</span>
                                @elseif(is_bool($val))
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-cairo {{ $val ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $val ? 'نعم' : 'لا' }}
                                    </span>
                                @elseif($isAmount && is_numeric($val))
                                    <span class="text-sm font-black text-emerald-700 font-cairo">
                                        {{ number_format((float) $val, 2) }} <span
                                            class="text-[10px] font-normal text-emerald-600">ريال</span>
                                    </span>
                                @elseif($key === 'status')
                                    <span class="px-3 py-1 rounded-xl text-xs font-bold bg-navy/10 text-navy font-cairo">
                                        {{ $val }}
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
