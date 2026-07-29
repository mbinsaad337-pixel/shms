@extends('pdf.layouts.master')

@section('styles')
    /* ============ إعدادات الصفحة والخطوط وتباعد الأسطر ============ */
    * { 
        box-sizing: border-box; 
    }
    
    body {
        font-family: 'Tajawal', 'Cairo', sans-serif;
        color: #0f172a;
        background-color: #ffffff; /* استخدام الخلفية البيضاء الصافية للطباعة الفاخرة */
        direction: rtl;
        margin: 0;
        padding: 0;
        font-size: 11px;
        line-height: 1.5; /* تباعد أسطر مثالي لمنع التداخل والالتصاق */
    }

    /* موازنة تباعد هوامش الصفحة العامة في الـ PDF */
    @page {
        margin: 20px 25px;
    }

    /* ============ كارت الهيدر الأبيض المطور (Shadow & High Contrast) ============ */
    .doc-header-card {
        width: 100%;
        border-collapse: collapse;
        background-color: #ffffff;
        border-top: 4px solid #002c4f; /* شريط كحلي عريض بالأعلى لجمالية التصميم */
        border-left: 1px solid #cbd5e1;
        border-right: 1px solid #cbd5e1;
        border-bottom: 3px solid #94a3b8; /* محاكاة تأثير الظل الساقط بطريقة متوافقة مع PDF */
        border-radius: 8px;
        margin-bottom: 15px; /* تقليل الهامش لتجنب الفراغات الزائدة */
    }

    .doc-header-card td {
        padding: 12px 15px;
        vertical-align: middle;
    }

    /* قسم الصورة الشخصية (يمين) */
    .doc-header-card .photo-cell {
        width: 100px;
        text-align: center;
        padding-left: 0;
    }

    .profile-photo {
        width: 80px;
        height: 100px;
        object-fit: cover;
        border: 1px solid #002c4f;
        border-radius: 6px;
        background-color: #ffffff;
    }

    /* قسم البيانات الأساسية عالي الوضوح (الوسط) */
    .doc-header-card .info-cell {
        text-align: right;
        padding-right: 15px;
    }

    .doc-header-card .info-cell .system-title {
        color: #475569;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.5px;
        margin-bottom: 3px;
        text-transform: uppercase;
    }

    .doc-header-card .info-cell h2 {
        margin: 0 0 4px 0;
        font-size: 18px;
        font-weight: 900;
        color: #002c4f;
    }

    .doc-header-card .info-cell .student-name {
        font-size: 14px;
        font-weight: 800;
        color: #7e5700; /* اللون الذهبي الأكاديمي المعتمد في البوابة */
        margin-bottom: 8px;
    }

    /* حاوية البيانات الأكاديمية المصغرة داخل الهيدر */
    .header-meta-table {
        border-collapse: collapse;
        margin-top: 3px;
    }

    .header-meta-table td {
        padding: 0;
        font-size: 10px;
    }

    .meta-item {
        background-color: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 3px 8px;
        color: #1e293b;
        font-weight: 700;
    }

    /* قسم الباركود والـ QR (يسار) */
    .doc-header-card .barcode-cell {
        width: 120px;
        text-align: center;
        border-right: 1px solid #f1f5f9;
        padding-right: 10px;
    }

    .doc-header-card .barcode-cell img {
        max-width: 90px;
        height: auto;
    }

    .doc-header-card .barcode-cell p {
        color: #334155;
        font-size: 8.5px;
        font-weight: 800;
        margin: 4px 0 0 0;
    }

    /* ============ كروت الأقسام والهيكل الموحد ============ */
    .card-academic {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-bottom: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px; /* تقليل الهامش بين الأقسام لمنع الفراغات الزائدة */
        /* أزلنا page-break-inside للسماح بانسياب الأقسام بحرية دون ترك مساحات بيضاء هائلة */
    }

    /* عناوين الأقسام بنمط Google Stitch الأنيق */
    .section-header-table {
        width: 100%;
        margin-bottom: 8px;
        border-collapse: collapse;
    }

    .section-header-table td {
        vertical-align: middle;
        padding: 0;
    }

    .section-indicator {
        width: 4px;
        background-color: #002c4f;
        height: 20px;
        border-radius: 2px;
    }

    .section-title-text {
        font-size: 11.5px;
        font-weight: 800;
        color: #002c4f;
        padding-right: 8px;
    }

    .sub-title {
        font-size: 10px;
        font-weight: 800;
        color: #7e5700;
        margin: 8px 0 4px;
        border-bottom: 1px dashed #e2e8f0;
        padding-bottom: 2px;
    }

    /* ============ تخطيط الجداول الداخلي ============ */
    .layout-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .layout-table > tbody > tr > td {
        vertical-align: top;
        padding: 0;
    }

    /* جداول البيانات الأساسية */
    .academic-table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
    }

    .academic-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 800;
        border-bottom: 2px solid #e2e8f0;
        padding: 5px 6px;
        font-size: 9px;
    }

    .academic-table td {
        border-bottom: 1px solid #f1f5f9;
        padding: 5px 6px;
        color: #0f172a;
        font-size: 9.5px;
    }

    /* جداول التفاصيل الثنائية Key-Value */
    .kv-table {
        width: 100%;
        border-collapse: collapse;
    }

    .kv-table td {
        padding: 4px 0;
        border-bottom: 1px solid #f8fafc;
        font-size: 9px;
        line-height: 1.4;
    }

    .kv-table .key {
        color: #64748b;
        font-weight: 700;
    }

    .kv-table .value {
        color: #0f172a;
        font-weight: 800;
        text-align: left;
    }

    /* ============ شارات الحالة ومؤشرات التقدم ============ */
    .status-badge {
        display: inline-block;
        padding: 1px 6px;
        border-radius: 12px;
        font-size: 8px;
        font-weight: 800;
    }

    .badge-success { background-color: #dcfce7; color: #15803d; }
    .badge-danger  { background-color: #fee2e2; color: #b91c1c; }

    /* شبكة الخلاصة المالية الموزعة بالتساوي */
    .financial-grid {
        width: 100%;
        border-collapse: separate;
        border-spacing: 6px 0;
        margin-bottom: 8px;
    }

    .financial-card {
        border-radius: 6px;
        padding: 8px 10px;
        text-align: right;
        border: 1px solid #e2e8f0;
        vertical-align: top;
    }

    .financial-card.primary {
        background-color: #002c4f;
        color: #ffffff;
        border: 1px solid #002c4f;
    }

    .financial-card .label {
        font-size: 8px;
        margin-bottom: 2px;
        display: block;
    }

    .financial-card.primary .label { color: #93c5fd; }
    .financial-card .amount { font-size: 12.5px; font-weight: 800; }

    /* جدول شريط التقدم المطور - مقاوم تماماً لانهيار الأبعاد في الـ PDF */
    .progress-table-container {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px;
        background-color: #ffffff;
        margin-bottom: 10px;
    }

    .progress-bar-table {
        width: 100%;
        border-collapse: collapse;
        height: 6px; /* تحكم صارم بالارتفاع لمنع التمدد */
        background-color: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-bar-table td {
        padding: 0;
        height: 6px;
    }

    /* الحالات الفارغة المنسقة */
    .empty-state-card {
        background-color: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 6px;
        padding: 15px;
        text-align: center;
    }

    .empty-state-card h4 { color: #475569; font-size: 10.5px; font-weight: 700; margin: 0 0 2px 0; }
    .empty-state-card p { color: #94a3b8; font-size: 8.5px; margin: 0; }

    .skill-badge {
        display: inline-block;
        background-color: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #002c4f;
        padding: 2px 6px;
        border-radius: 12px;
        font-size: 8.5px;
        font-weight: 700;
        margin-left: 3px;
        margin-bottom: 3px;
    }

    /* ============ تذييل التوقيعات والاعتماد ============ */
    .signature-section {
        margin-top: 20px;
        padding-top: 10px;
        border-top: 1px solid #cbd5e1;
        page-break-inside: avoid; /* منع تفكك صندوق التوقيعات */
    }

    .signature-table {
        width: 100%;
        border-collapse: collapse;
    }

    .signature-table td {
        width: 33.33%;
        text-align: center;
        vertical-align: top;
        padding: 5px;
    }

    .signature-line {
        border-top: 1px solid #475569;
        width: 70%;
        margin: 20px auto 4px;
    }

    .signature-title { font-size: 9px; font-weight: 800; color: #002c4f; }
    .signature-subtitle { font-size: 8px; color: #64748b; }
@endsection

@section('content')

    {{-- ===================== رأس الوثيقة (كرت عالي الوضوح والتباين والجاذبية) ===================== --}}
    <table class="doc-header-card">
        <tr>
            <!-- 1) الصورة الشخصية للطالب (اليمين) -->
            <td class="photo-cell">
                @if($student->photo)
                    <img src="{{ $photoBase64 }}" alt="صورة الطالب" class="profile-photo">
                @else
                    <div class="profile-photo" style="background-color: #f1f5f9; display: table; text-align: center;">
                        <span style="display: table-cell; vertical-align: middle; color: #94a3b8; font-size: 10px;">لا توجد صورة</span>
                    </div>
                @endif
            </td>
            
            <!-- 2) البيانات الشخصية الأساسية بوضوح تام وتصميم عصري (الوسط) -->
            <td class="info-cell">
                <div class="system-title">نظام إدارة المراكز الطلابية</div>
                <div class="student-name"><h2>{{ $student->name_ar }} {{ $student->surname }}</h2></div>
                
                <table class="header-meta-table">
                    <tr>
                        <td style="padding-left: 8px;">
                            <div class="meta-item">
                                <span style="color: #64748b;">الرقم الجامعي:</span> {{ $student->student_number }}
                            </div>
                        </td>
                        <td>
                            <div class="meta-item">
                                <span style="color: #64748b;">البرنامج:</span> {{ $student->program->name ?? 'غير محدد' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            
            <!-- 3) الباركود (اليسار) -->
            <td class="barcode-cell">
                @if($barcodeBase64)
                    <img src="{{ $barcodeBase64 }}" alt="باركود الطالب">
                    <p>{{ $student->barcode }}</p>
                @endif
            </td>
        </tr>
    </table>

    {{-- ===================== 1) البيانات الشخصية والعدلية ===================== --}}
    <div class="card-academic">
        <table class="section-header-table">
            <tr>
                <td class="section-indicator"></td>
                <td class="section-title-text">أولاً: البيانات الشخصية</td>
            </tr>
        </table>

        <table class="layout-table">
            <tr>
                <!-- جدول البيانات الرئيسي (اليمين) -->
                <td style="width: 63%; padding-left: 12px;">
                    <table class="academic-table">
                        <thead>
                            <tr>
                                <th>البيان</th>
                                <th>التفاصيل ومستندات التحقق</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-weight: 700; color: #475569;">الاسم بالكامل</td>
                                <td>{{ $student->name_ar }} {{ $student->surname }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700; color: #475569;">الجنسية والتبعية</td>
                                <td>{{ $student->nationality }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700; color: #475569;">تاريخ ومكان الميلاد</td>
                                <td>{{ $student->date_of_birth?->format('Y-m-d') }} ({{ $student->place_of_birth }})</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700; color: #475569;">فصيلة الدم</td>
                                <td style="color: #b91c1c; font-weight: 800;">{{ $student->blood_type }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: 700; color: #475569;">الحالة الصحية العامة</td>
                                <td>
                                    <span class="status-badge badge-success">
                                        {{ $student->health_status ?? 'لائق طبياً' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                <!-- قنوات التواصل والعناوين (اليسار) -->
                <td style="width: 37%;">
                    <div style="border-radius: 6px; padding: 8px; margin-bottom: 6px; ">
                        <div style="color: #7e5700; font-weight: 800; font-size: 9px; margin-bottom: 4px;">
                            📞 معلومات الاتصال السريع
                        </div>
                        <br>
                        <div style="margin-bottom: 4px;">
                            <span style="font-size: 7.5px; color: #64748b; display:block;">رقم الجوال النشط</span>
                            <span style="font-weight: 800; font-size: 9.5px; color: #0f172a;">{{ $student->phone }}</span>
                        </div>
                        <div>
                            <span style="font-size: 7.5px; color: #64748b; display:block;">البريد الإلكتروني</span>
                            <span style="font-weight: 800; font-size: 9.5px; color: #0f172a;">{{ $student->email }}</span>
                        </div>
                    </div>

                    <div style="border-radius: 6px; padding: 8px; ">
                        <div style="color: #7e5700; font-weight: 800; font-size: 9px; margin-bottom: 3px;">
                            📍 العنوان الوطني الدائم
                        </div>
                        <span style="font-size: 8.5px; line-height: 1.3; display:block; color: #334155; font-weight: 700;">
                            {{ $student->governorate }}، {{ $student->district }}، {{ $student->permanent_address }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===================== 2) البيانات الأكاديمية ===================== --}}
    <div class="card-academic">
        <table class="section-header-table">
            <tr>
                <td class="section-indicator"></td>
                <td class="section-title-text">ثانياً: البيانات الأكاديمية والجامعية</td>
            </tr>
        </table>

        <!-- شريط الحالة الأكاديمية السريع -->
        <div style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 8px; background-color: #f1f5f9; margin-bottom: 10px;">
            <table style="width: 100%; border-collapse: collapse; text-align: center;">
                <tr>
                    <td style="border-left: 1px solid #cbd5e1; width: 25%;">
                        <div style="font-size: 7.5px; color: #64748b; margin-bottom: 1px;">الجامعة</div>
                        <div style="font-weight: 800; color: #002c4f; font-size: 10px;">{{ $student->university }}</div>
                    </td>
                    <td style="border-left: 1px solid #cbd5e1; width: 25%;">
                        <div style="font-size: 7.5px; color: #64748b; margin-bottom: 1px;">الكلية</div>
                        <div style="font-weight: 800; color: #002c4f; font-size: 10px;">{{ $student->college }}</div>
                    </td>
                    <td style="border-left: 1px solid #cbd5e1; width: 25%;">
                        <div style="font-size: 7.5px; color: #64748b; margin-bottom: 1px;">التخصص الدقيق</div>
                        <div style="font-weight: 800; color: #002c4f; font-size: 10px;">{{ $student->major }}</div>
                    </td>
                    <td style="width: 25%;">
                        <div style="font-size: 7.5px; color: #64748b; margin-bottom: 1px;">المستوى الحالي</div>
                        <div style="font-weight: 800; color: #002c4f; font-size: 10px;">{{ $student->academic_level }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="layout-table">
            <tr>
                <!-- تفاصيل التسجيل (اليمين) -->
                <td style="width: 50%; padding-left: 8px;">
                    <div class="sub-title">تفاصيل القيد والتسجيل</div>
                    <table class="kv-table">
                        <tr>
                            <td class="key">تاريخ القبول والالتحاق:</td>
                            <td class="value">{{ $student->enrollment_date?->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <td class="key">المدة المعتمدة للدراسة:</td>
                            <td class="value">{{ $student->study_duration }} سنوات دبلوم/بكالوريوس</td>
                        </tr>
                        <tr>
                            <td class="key">الفصل الدراسي الجاري:</td>
                            <td class="value">{{ $student->current_academic_year }}</td>
                        </tr>
                    </table>
                </td>

                <!-- المؤهلات السابقة (اليسار) -->
                <td style="width: 50%; padding-right: 8px;">
                    <div class="sub-title">المؤهل الدراسي والشهادة السابقة</div>
                    <table class="kv-table">
                        <tr>
                            <td class="key">الشهادة / المؤهل السابق:</td>
                            <td class="value">{{ $student->last_certificate }}</td>
                        </tr>
                        <tr>
                            <td class="key">مدرسة التخرج السابقة:</td>
                            <td class="value">{{ $student->graduated_school }}</td>
                        </tr>
                        <tr>
                            <td class="key">معدل وسنة التخرج:</td>
                            <td class="value">{{ $student->graduation_year }} ({{ $student->last_cert_major }})</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===================== 3) السكن الجامعي ===================== --}}
    <div class="card-academic">
        <table class="section-header-table">
            <tr>
                <td class="section-indicator"></td>
                <td class="section-title-text">ثالثاً: السكن الجامعي والداخلي</td>
            </tr>
        </table>

        @if($student->activeRoomAssignment && $student->activeRoomAssignment->room)
            <table class="academic-table">
                <thead>
                    <tr>
                        <th>المبنى السكني</th>
                        <th>الطابق</th>
                        <th>الشقة السكنية</th>
                        <th>رقم الغرفة المخصصة</th>
                        <th>تاريخ استلام المفاتيح والتخصيص</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $student->activeRoomAssignment->room->building }}</td>
                        <td>{{ $student->activeRoomAssignment->room->floor }}</td>
                        <td>{{ $student->activeRoomAssignment->room->apartment }}</td>
                        <td style="font-weight: 800; color: #002c4f; font-size: 10px;">{{ $student->activeRoomAssignment->room->room_number }}</td>
                        <td>{{ $student->activeRoomAssignment->assigned_at?->format('Y-m-d') }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="empty-state-card">
                <h4>الطالب غير مسكن حالياً بالوحدات الداخلية</h4>
                <p>لم يتم العثور على سجلات سارية لتخصيص أو حجز غرف للطالب في نظام الإسكان الطلابي الحالي.</p>
            </div>
        @endif
    </div>

    {{-- ===================== 4) الخلاصة المالية ===================== --}}
    <div class="card-academic">
        <table class="section-header-table">
            <tr>
                <td class="section-indicator"></td>
                <td class="section-title-text">رابعاً: السجل المالي وباقات التغذية</td>
            </tr>
        </table>

        <!-- بطاقات السجل المالي (Bento Layout) -->
        <table class="financial-grid">
            <tr>
                <td class="financial-card primary">
                    <span class="label">إجمالي الرسوم السنوية</span>
                    <span class="amount">{{ number_format($student->annual_fees, 2) }} ر.ي</span>
                </td>
                <td class="financial-card">
                    <span class="label" style="color: #15803d; font-weight: 800;">المبالغ المودعة والمسددة</span>
                    <span class="amount" style="color: #15803d;">{{ number_format($totalPaid, 2) }} ر.ي</span>
                </td>
                <td class="financial-card">
                    <span class="label" style="color: #b91c1c; font-weight: 800;">المبالغ المستحقة والمتبقية</span>
                    <span class="amount" style="color: #b91c1c;">{{ number_format($remainingFees, 2) }} ر.ي</span>
                </td>
            </tr>
        </table>

        <!-- مؤشر السداد باستخدام جدول مقاوم للانهيار (100% متوافق مع DomPDF) -->
        @php
            $percentPaid = $student->annual_fees > 0 ? min(100, round(($totalPaid / $student->annual_fees) * 100)) : 0;
            $percentRemaining = 100 - $percentPaid;
        @endphp
        <div class="progress-table-container">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 2px;">
                <tr>
                    <td style="font-size: 9px; font-weight: 800; color: #002c4f;">مؤشر سداد الالتزامات المالية للرسوم</td>
                    <td style="text-align: left; font-size: 9px; font-weight: 800; color: #15803d;">{{ $percentPaid }}%</td>
                </tr>
            </table>
            
            <table class="progress-bar-table">
                <tr>
                    @if($percentPaid > 0)
                        <td style="width: {{ $percentPaid }}%; background-color: #15803d;"></td>
                    @endif
                    @if($percentRemaining > 0)
                        <td style="width: {{ $percentRemaining }}%; background-color: #e2e8f0;"></td>
                    @endif
                </tr>
            </table>
        </div>
        <br>

        <!-- باقة التغذية المصاحبة -->
        @if($student->mealSubscription)
            <div class="sub-title">الاشتراك النشط في باقات التغذية</div>
            <table class="academic-table">
                <thead>
                    <tr>
                        <th>اسم باقة الاشتراك المعتمدة</th>
                        <th>تاريخ التفعيل</th>
                        <th>تاريخ انتهاء الخدمة</th>
                        <th>المستحق الإجمالي</th>
                        <th>المدفوع الفعلي</th>
                        <th>حالة الاشتراك</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight: 700;">باقة التغذية الموحدة للمركز</td>
                        <td>{{ $student->mealSubscription->start_date?->format('Y-m-d') }}</td>
                        <td>{{ $student->mealSubscription->end_date?->format('Y-m-d') }}</td>
                        <td>{{ number_format($student->mealSubscription->total_due, 2) }} ر.ي</td>
                        <td>{{ number_format($student->mealSubscription->total_paid, 2) }} ر.ي</td>
                        <td>
                            @php
                                $status = $student->mealSubscription->status;
                                $badgeClass = match($status) {
                                    'active', 'نشط' => 'badge-success',
                                    'expired', 'منتهي' => 'badge-danger',
                                    default => 'badge-warning',
                                };
                            @endphp
                            <span class="status-badge {{ $badgeClass }}">{{ $status }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>

    {{-- ===================== 5) جهات الاتصال الطارئة وولي الأمر ===================== --}}
    <div class="card-academic">
        <table class="section-header-table">
            <tr>
                <td class="section-indicator"></td>
                <td class="section-title-text">خامساً: بيانات ولي الأمر والاتصالات الطارئة</td>
            </tr>
        </table>

        <table class="layout-table">
            <tr>
                <!-- بيانات ولي الأمر (اليمين) -->
                <td style="width: 50%; padding-left: 8px;">
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; background-color: #ffffff; height: 100%;">
                        <div style="color: #7e5700; font-weight: 800; font-size: 10px; margin-bottom: 6px; border-bottom: 1px solid #f1f5f9; padding-bottom: 3px;">
                            📋 البيانات الثبوتية لولي الأمر
                        </div>
                        <table class="kv-table">
                            <tr><td class="key">الاسم الرباعي:</td><td class="value">{{ $student->guardian_name }}</td></tr>
                            <tr><td class="key">صلة القرابة المباشرة:</td><td class="value">{{ $student->guardian_relation }}</td></tr>
                            <tr><td class="key">المهنة وجهة العمل:</td><td class="value">{{ $student->guardian_job }}</td></tr>
                            <tr><td class="key">المؤهل التعليمي:</td><td class="value">{{ $student->guardian_education }}</td></tr>
                            <tr><td class="key">رقم هاتف التواصل:</td><td class="value" style="color: #002c4f;">{{ $student->guardian_phone }}</td></tr>
                        </table>
                    </div>
                </td>

                <!-- جهات الاتصال البديلة في الطوارئ (اليسار) -->
                <td style="width: 50%; padding-right: 8px;">
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; background-color: #ffffff; height: 100%;">
                        <div style="color: #7e5700; font-weight: 800; font-size: 10px; margin-bottom: 6px; border-bottom: 1px solid #f1f5f9; padding-bottom: 3px;">
                            🚨 المسؤول عن الطالب في حالات الطوارئ
                        </div>
                        <table class="kv-table" style="margin-top: 5px;">
                            <tr><td class="key">الاسم الكامل للمسؤول:</td><td class="value">{{ $student->emergency_name }}</td></tr>
                            <tr><td class="key">درجة صلة القرابة:</td><td class="value">{{ $student->emergency_relation }}</td></tr>
                            <tr><td class="key">رقم هاتف الطوارئ المباشر:</td><td class="value" style="color: #b91c1c; font-size: 10px;">{{ $student->emergency_phone }}</td></tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===================== 6) بيانات إضافية ومهارات ===================== --}}
    <div class="card-academic">
        <table class="section-header-table">
            <tr>
                <td class="section-indicator"></td>
                <td class="section-title-text">سادساً: الإحصاءات الأسرية والمهارات الشخصية</td>
            </tr>
        </table>

        <table class="layout-table">
            <tr>
                <!-- إحصاءات عائلية (اليمين) -->
                <td style="width: 50%; padding-left: 8px;">
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; height: 100%;">
                        <div style="color: #7e5700; font-weight: 800; font-size: 9.5px; margin-bottom: 5px;">
                            الهيكل الأسري والاقتصادي للأسرة
                        </div>
                        <table class="kv-table">
                            <tr><td class="key">عدد الذكور في الأسرة:</td><td class="value">{{ $student->family_males }} أفراد</td></tr>
                            <tr><td class="key">عدد الإناث في الأسرة:</td><td class="value">{{ $student->family_females }} أفراد</td></tr>
                            <tr><td class="key">إجمالي التابعين والمعالين:</td><td class="value">{{ $student->dependents_count }} أفراد معالين</td></tr>
                            <tr><td class="key">متوسط الدخل الشهري التقريبي:</td><td class="value">{{ number_format($student->family_avg_income, 2) }} ر.ي</td></tr>
                        </table>
                    </div>
                </td>

                <!-- المهارات والهوايات (اليسار) -->
                <td style="width: 50%; padding-right: 8px;">
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; height: 100%; background-color: #ffffff;">
                        <div style="color: #7e5700; font-weight: 800; font-size: 9.5px; margin-bottom: 6px;">
                            المهارات والقدرات الخاصة الموثقة
                        </div>
                        <div style="margin-top: 6px; line-height: 1.5;">
                            @if($student->skills)
                                @foreach(explode(',', $student->skills) as $skill)
                                    <span class="skill-badge">{{ trim($skill) }}</span>
                                @endforeach
                            @else
                                <span class="skill-badge" style="color: #64748b;">لا توجد مهارات مسجلة</span>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- أفراد الأسرة العاملين -->
        @if($student->family_workers && count($student->family_workers) > 0)
            <div class="sub-title" style="margin-top: 10px;">أفراد العائلة العاملين والموثقين لدى المركز</div>
            <table class="academic-table">
                <thead>
                    <tr>
                        <th>الاسم الكامل للقرابة</th>
                        <th>الوظيفة والمهنة الحالية</th>
                        <th>المؤسسة / جهة العمل والتوظيف</th>
                        <th>رقم الهاتف المسجل</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($student->family_workers as $worker)
                    <tr>
                        <td style="font-weight: 700;">{{ $worker['name'] }}</td>
                        <td>{{ $worker['job'] }}</td>
                        <td>{{ $worker['organization'] }}</td>
                        <td>{{ $worker['phone'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ===================== التوقيعات والاعتماد ===================== --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-title">أعده وصاغه / مسؤول مدخلات البيانات</div>
                    <div class="signature-line"></div>
                    <div class="signature-subtitle">الاسم والتوقيع: {{ $exportUser ?? 'مسؤول النظام المعتمد' }}</div>
                </td>
                <td>
                    <div class="signature-title">راجعه وصادقه / المسؤول الإداري المباشر</div>
                    <div class="signature-line"></div>
                    <div class="signature-subtitle">التوقيع، والتاريخ، وختم القسم المعتمد</div>
                </td>
                <td>
                    <div class="signature-title">اعتمد من قِبل / إدارة المركز الطلابي</div>
                    <div class="signature-line"></div>
                    <div class="signature-subtitle">الاعتماد النهائي والمصادقة الأكاديمية</div>
                </td>
            </tr>
        </table>

        <!-- الفوتر والتذييل الزمني للطباعة -->
        <table style="width: 100%; margin-top: 15px; border-collapse: collapse;">
            <tr>
                <td style="text-align: right; font-size: 8px; color: #94a3b8;">
                    بوابة إدارة السجلات الأكاديمية والمراكز الطلابية الموحدة © {{ date('Y') }}. جميع الحقوق محفوظة ومحمية.
                </td>
                <td style="text-align: left; font-size: 8px; color: #94a3b8;">
                    تاريخ استخراج التقرير الفعلي: <span style="font-weight: 700; color: #475569;">{{ now()->format('Y-m-d H:i') }}</span>
                </td>
            </tr>
        </table>
    </div>
@endsection