@extends('pdf.layouts.master')

@section('styles')
<style>
    .record-identity {
        border: 1px solid #004274;
        padding: 14px 16px;
        margin-bottom: 14px;
        background: #f8fafc;
    }
    .record-title {
        font-size: 16px;
        font-weight: bold;
        color: #004274;
        margin-bottom: 6px;
    }
    .record-sub {
        font-size: 10px;
        color: #64748b;
    }
    .badge-module {
        display: inline-block;
        background: #004274;
        color: #ffffff;
        padding: 3px 10px;
        font-size: 10px;
        font-weight: bold;
        border-radius: 3px;
        margin-left: 8px;
    }
    .badge-year {
        display: inline-block;
        background: #D4A044;
        color: #ffffff;
        padding: 3px 10px;
        font-size: 10px;
        font-weight: bold;
        border-radius: 3px;
    }
    .archive-ref {
        float: left;
        text-align: left;
        font-size: 10px;
        color: #004274;
    }
    .archive-ref .num {
        font-size: 15px;
        font-weight: bold;
    }
    .section-header {
        background: #004274;
        color: #ffffff;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: bold;
        margin: 16px 0 10px;
        border-right: 4px solid #D4A044;
    }
    .detail-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6px;
    }
    .detail-table td {
        border: 1px solid #e2e8f0;
        padding: 7px 10px;
        vertical-align: top;
        font-size: 10.5px;
    }
    .detail-table .key {
        width: 22%;
        background: #f8fafc;
        color: #64748b;
        font-weight: bold;
    }
    .detail-table .value {
        color: #0f172a;
        font-weight: bold;
    }
    .detail-table .value.full {
        width: 78%;
    }
    .value-amount {
        color: #15803d;
        font-weight: bold;
    }
    .value-long {
        line-height: 1.8;
        white-space: pre-wrap;
    }
    .value-empty {
        color: #94a3b8;
        font-weight: normal;
    }
    .subtable {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
        font-size: 9.5px;
    }
    .subtable thead th {
        background: #eef2f7;
        color: #004274;
        padding: 6px 8px;
        border: 1px solid #d8dee8;
        font-weight: bold;
    }
    .subtable tbody td {
        padding: 6px 8px;
        border: 1px solid #e2e8f0;
        color: #334155;
    }
    .subtable tbody tr.even {
        background: #fafbfc;
    }
    .notice {
        background: #fef3c7;
        border: 1px solid #fde68a;
        padding: 10px 12px;
        font-size: 10px;
        color: #854d0e;
        margin-bottom: 12px;
    }
    .signatures-table {
        margin-top: 40px;
    }
</style>
@endsection

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
        'location' => 'المكان / الموقع',
        'views_count' => 'عدد المشاهدات',
    ];
@endphp

@section('content')

    {{-- ════ RECORD IDENTITY ════ --}}
    <div class="record-identity">
        <div class="archive-ref">
            <div>معرف الأرشيف المرجعي</div>
            <div class="num">#ARC-{{ str_pad($archive->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div>
            <span class="badge-module">{{ $moduleName }}</span>
            <span class="badge-year">سنة {{ $archive->year }}</span>
        </div>
        <div class="record-title">{{ $archive->title }}</div>
        <div class="record-sub">
            المركز: {{ optional($archive->center)->name ?? 'المركز العام' }}
            @if($archive->student_name || optional($archive->student)->name_ar)
                &nbsp;|&nbsp; الطالب: {{ $archive->student_name ?: optional($archive->student)->name_ar }}
            @endif
        </div>
    </div>

    {{-- ════ RECORD SUMMARY ════ --}}
    <div class="section-header">معلومات السجل الأساسية</div>
    <table class="detail-table">
        <tr>
            <td class="key">تاريخ السجل الأصلي</td>
            <td class="value">{{ $archive->record_date ? $archive->record_date->format('Y/m/d H:i') : '-' }}</td>
            <td class="key">تاريخ الأرشفة</td>
            <td class="value">{{ $archive->created_at->format('Y/m/d H:i') }}</td>
        </tr>
        <tr>
            <td class="key">الطالب المعني</td>
            <td class="value">{{ $archive->student_name ?: (optional($archive->student)->name_ar ?: 'غير محدد / سجل عام') }}</td>
            <td class="key">المنفذ للترحيل</td>
            <td class="value">{{ optional(optional($archive->rollover)->user)->name ?? 'النظام' }}</td>
        </tr>
        @if($archive->amount > 0)
        <tr>
            <td class="key">المبلغ المالي</td>
            <td class="value value-amount">{{ number_format($archive->amount, 2) }} ريال</td>
            <td class="key"></td>
            <td class="value"></td>
        </tr>
        @endif
    </table>

    @php
        $isAmountKey = fn($k) => preg_match('/(amount|cost|budget|fine|income|expense|balance|price)/i', $k);
        $isLongKey = fn($k) => in_array($k, ['description', 'body', 'notes', 'reason', 'response', 'subject', 'title']);
        $pairs = [];
        $i = 0;
    @endphp

    {{-- ════ SCALAR FIELDS ════ --}}
    @if(count($scalarData) > 0)
        <div class="section-header">بيانات وتفاصيل السجل المؤرشف بالكامل</div>
        <table class="detail-table">
            @foreach($scalarData as $key => $val)
                @if($key === 'id' || $key === 'created_at' || $key === 'updated_at' || $key === 'center_id' || $key === 'student_id')
                    @continue
                @endif
                @php
                    $label = $keysMap[$key] ?? $key;
                    $longValue = $isLongKey($key) || (is_string($val) && mb_strlen($val) > 80);
                @endphp
                @if($longValue)
                    @if($i % 2 !== 0)
                        <td class="key"></td><td class="value full"></td></tr>
                    @endif
                    <tr>
                        <td class="key">{{ $label }}</td>
                        <td class="value full value-long">
                            @if($val === null || $val === '')
                                <span class="value-empty">--</span>
                            @elseif(is_bool($val))
                                {{ $val ? 'نعم' : 'لا' }}
                            @elseif($isAmountKey($key) && is_numeric($val))
                                <span class="value-amount">{{ number_format((float)$val, 2) }} ريال</span>
                            @else
                                {{ $val }}
                            @endif
                        </td>
                    </tr>
                    @php $i = 0; @endphp
                @else
                    @if($i % 2 === 0)
                        <tr>
                            <td class="key">{{ $label }}</td>
                            <td class="value">
                                @if($val === null || $val === '')
                                    <span class="value-empty">--</span>
                                @elseif(is_bool($val))
                                    {{ $val ? 'نعم' : 'لا' }}
                                @elseif($isAmountKey($key) && is_numeric($val))
                                    <span class="value-amount">{{ number_format((float)$val, 2) }} ريال</span>
                                @else
                                    {{ $val }}
                                @endif
                            </td>
                    @else
                            <td class="key">{{ $label }}</td>
                            <td class="value">
                                @if($val === null || $val === '')
                                    <span class="value-empty">--</span>
                                @elseif(is_bool($val))
                                    {{ $val ? 'نعم' : 'لا' }}
                                @elseif($isAmountKey($key) && is_numeric($val))
                                    <span class="value-amount">{{ number_format((float)$val, 2) }} ريال</span>
                                @else
                                    {{ $val }}
                                @endif
                            </td>
                        </tr>
                    @endif
                    @php $i++; @endphp
                @endif
            @endforeach
            @if($i % 2 !== 0)
                <td class="key"></td><td class="value full"></td></tr>
            @endif
        </table>
    @endif

    {{-- ════ COMPLEX SUB-TABLES ════ --}}
    @if(count($complexData) > 0)
        @foreach($complexData as $key => $subItems)
            @if(is_array($subItems) && count($subItems) > 0)
                @php
                    $label = $keysMap[$key] ?? $key;
                    $isTabular = isset($subItems[0]) && is_array($subItems[0]);
                @endphp
                <div class="section-header">{{ $label }} ({{ count($subItems) }} سجل)</div>

                @if($isTabular)
                    @php $headers = array_keys($subItems[0]); @endphp
                    <table class="subtable">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                @foreach($headers as $colKey)
                                    @if($colKey !== 'id' && !is_array($subItems[0][$colKey]))
                                        <th>{{ $keysMap[$colKey] ?? $colKey }}</th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subItems as $idx => $row)
                                <tr class="{{ $idx % 2 == 0 ? 'even' : 'odd' }}">
                                    <td>{{ $idx + 1 }}</td>
                                    @foreach($row as $colKey => $cellVal)
                                        @if($colKey !== 'id' && !is_array($cellVal))
                                            <td>
                                                @if($cellVal === null || $cellVal === '')
                                                    <span class="value-empty">-</span>
                                                @elseif(is_bool($cellVal))
                                                    {{ $cellVal ? 'نعم' : 'لا' }}
                                                @elseif(is_numeric($cellVal) && preg_match('/(amount|price|cost|total|balance)/i', $colKey))
                                                    <span class="value-amount">{{ number_format((float)$cellVal, 2) }} ريال</span>
                                                @else
                                                    {{ $cellVal }}
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <table class="detail-table">
                        @foreach($subItems as $subKey => $subVal)
                            @if(is_array($subVal))
                                <tr>
                                    <td class="key">{{ $keysMap[$subKey] ?? $subKey }}</td>
                                    <td class="value full value-long">{{ json_encode($subVal, JSON_UNESCAPED_UNICODE) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td class="key">{{ $keysMap[$subKey] ?? $subKey }}</td>
                                    <td class="value full">
                                        @if($subVal === null || $subVal === '')
                                            <span class="value-empty">--</span>
                                        @elseif(is_bool($subVal))
                                            {{ $subVal ? 'نعم' : 'لا' }}
                                        @else
                                            {{ $subVal }}
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                @endif
            @endif
        @endforeach
    @endif

    {{-- ════ ROLLOVER AUDIT ════ --}}
    @if($archive->rollover)
        <div class="section-header">بيانات عملية الترحيل السنوي المرتبطة</div>
        <table class="detail-table">
            <tr>
                <td class="key">السنة المرحلة</td>
                <td class="value">{{ $archive->rollover->year }}</td>
                <td class="key">تاريخ ووقت التنفيذ</td>
                <td class="value">{{ $archive->rollover->created_at->format('Y/m/d - H:i:s') }}</td>
            </tr>
            <tr>
                <td class="key">المستخدم المنفذ للعملية</td>
                <td class="value">{{ optional($archive->rollover->user)->name ?? 'النظام' }}</td>
                <td class="key"></td>
                <td class="value"></td>
            </tr>
        </table>
        @if($archive->rollover->notes)
            <div class="notice">
                <strong>ملاحظات الترحيل السنوي:</strong><br>
                {{ $archive->rollover->notes }}
            </div>
        @endif
    @endif

    {{-- ════ SIGNATURES ════ --}}
    <table class="signatures-table avoid-break">
        <tr>
            <td>
                <div class="sign-title">المسؤول المالي</div>
                <div class="sign-line"></div>
                <div class="sign-name"></div>
            </td>
            <td>
                <div class="sign-title">مدير المركز</div>
                <div class="sign-line"></div>
                <div class="sign-name"></div>
            </td>
            <td>
                <div class="sign-title">مسؤول الأرشيف والترحيل</div>
                <div class="sign-line"></div>
                <div class="sign-name">{{ optional(optional($archive->rollover)->user)->name ?? '' }}</div>
            </td>
        </tr>
    </table>

@endsection
