<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-size: 14px;
            direction: rtl;
            text-align: right;
            color: #333;
            line-height: 1.6;
        }

        .header-container {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #2d3748;
            padding-bottom: 15px;
        }

        .klisha-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .klisha-right {
            width: 33%;
            text-align: right;
            font-size: 14px;
        }

        .klisha-center {
            width: 34%;
            text-align: center;
        }

        .klisha-left {
            width: 33%;
            text-align: left;
            font-size: 12px;
            color: #718096;
        }

        .main-title {
            font-size: 24px;
            font-weight: bold;
            color: #2d3748;
            margin: 0;
        }

        .student-photo {
            width: 120px;
            height: 150px;
            border: 1px solid #cbd5e0;
            display: block;
            margin-left: 0;
            margin-right: auto;
            object-fit: cover;
        }

        .photo-placeholder {
            width: 120px;
            height: 150px;
            border: 1px dashed #cbd5e0;
            background-color: #f7fafc;
            line-height: 150px;
            text-align: center;
            font-size: 12px;
            color: #a0aec0;
        }

        .section-box {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .section-header {
            background-color: #f8fafc;
            padding: 10px 15px;
            border-bottom: 1px solid #e2e8f0;
            font-weight: bold;
            color: #2c5282;
            font-size: 16px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .label {
            color: #718096;
            font-weight: normal;
            width: 20%;
        }

        .value {
            font-weight: bold;
            width: 30%;
        }

        .full-width {
            width: 80% !important;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #a0aec0;
            margin-top: 30px;
            border-top: 1px solid #edf2f7;
            padding-top: 10px;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            color: #fff;
        }

        .status-residing {
            background-color: #48bb78;
        }

        .status-registered {
            background-color: #4299e1;
        }

        .grid-container {
            width: 100%;
        }

        .grid-container td {
            vertical-align: top;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    @include('partials.pdf_header', [
        'title' => 'ملف بيانات طالب',
        'number' => $student->university_id ?? $student->barcode,
        'department' => 'شؤون الطلاب - ' . ($student->center->name ?? 'المركز'),
    ])

    <!-- المعلومات الأساسية والصورة -->
    <table class="grid-container" style="margin-bottom: 20px;">
        <tr>
            <td style="width: 75%;">
                <div class="section-box">
                    <div class="section-header">المعلومات الشخصية</div>
                    <table class="data-table">
                        <tr>
                            <td class="label">اسم الطالب:</td>
                            <td class="value" colspan="3" style="font-size: 18px;">{{ $student->name_ar }}</td>
                        </tr>
                        <tr>
                            <td class="label">الهوية الوطنية:</td>
                            <td class="value">{{ $student->national_id }}</td>
                            <td class="label">الجنسية:</td>
                            <td class="value">{{ $student->nationality }}</td>
                        </tr>
                        <tr>
                            <td class="label">رقم الجوال:</td>
                            <td class="value" dir="ltr">{{ $student->phone }}</td>
                            <td class="label">البريد الإلكتروني:</td>
                            <td class="value">{{ $student->email ?? $student->user->email }}</td>
                        </tr>
                        <tr>
                            <td class="label">الرقم الجامعي:</td>
                            <td class="value">{{ $student->student_number }}</td>
                            <td class="label">حالة السكن:</td>
                            <td class="value">
                                @if ($student->status == 'residing')
                                    مقيم
                                @elseif($student->status == 'registered')
                                    {{ $student->is_profile_approved ? 'مقيم' : 'حجز مبدئي' }}
                                @elseif($student->status == 'left')
                                    غادر
                                @elseif($student->status == 'graduated')
                                    متخرج
                                @elseif($student->status == 'suspended')
                                    موقوف
                                @else
                                    {{ $student->status }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width: 25%; text-align: left;">
                @php
                    $photoPath = $student->photo
                        ? $student->photo
                        : ($student->user->avatar
                            ? $student->user->avatar
                            : null);
                @endphp
                @if ($photoPath && file_exists(storage_path('app/public/' . $photoPath)))
                    <img src="{{ storage_path('app/public/' . $photoPath) }}" class="student-photo">
                @else
                    <div class="photo-placeholder">لا توجد صورة رسمية</div>
                @endif
            </td>
        </tr>
    </table>

    <div class="section-box">
        <div class="section-header">البيانات الأكاديمية</div>
        <table class="data-table">
            <tr>
                <td class="label">الجامعة:</td>
                <td class="value">{{ $student->university }}</td>
                <td class="label">الكلية:</td>
                <td class="value">{{ $student->college }}</td>
            </tr>
            <tr>
                <td class="label">التخصص:</td>
                <td class="value">{{ $student->major }}</td>
                <td class="label">المستوى:</td>
                <td class="value">{{ $student->academic_level }}</td>
            </tr>
        </table>
    </div>

    <!-- Grades -->
    @if ($student->grades->count() > 0)
        <div class="section-box">
            <div class="section-header">بيان الدرجات</div>
            <table class="data-table" style="text-align: center;">
                <tr style="background-color: #f1f5f9;">
                    <td class="value">العام الأكاديمي</td>
                    <td class="value">الفصل الدراسي</td>
                    <td class="value">المعدل / النسبة</td>
                </tr>
                @foreach ($student->grades as $grade)
                    <tr>
                        <td>{{ $grade->academic_year }}</td>
                        <td>{{ $grade->semester }}</td>
                        <td style="color: #2f855a;">{{ number_format($grade->gpa_percentage, 2) }}%</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <div class="section-box">
        <div class="section-header">بيانات السكن والطوارئ</div>
        <table class="data-table">
            <tr>
                <td class="label">الغرفة:</td>
                <td class="value" colspan="3">
                    @php $assignment = $student->roomAssignments->where('released_at', null)->first(); @endphp
                    @if ($assignment)
                        مبنى: {{ $assignment->room->building }} | رقم الغرفة: {{ $assignment->room->room_number }}
                        @if ($assignment->room->apartment)
                            | شقة: {{ $assignment->room->apartment }}
                        @endif
                    @else
                        غير مسكن
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">جهة الاتصال:</td>
                <td class="value">{{ $student->emergency_name }}</td>
                <td class="label">الصلة:</td>
                <td class="value">{{ $student->emergency_relation }}</td>
            </tr>
            <tr>
                <td class="label">جوال الطوارئ:</td>
                <td class="value" colspan="3" dir="ltr" style="color: #e53e3e;">
                    {{ $student->emergency_phone }}</td>
            </tr>
        </table>
    </div>

    <!-- Violations -->
    @if ($student->violations->count() > 0)
        <div class="section-box">
            <div class="section-header">السجل السلوكي والمخالفات</div>
            <table class="data-table">
                <tr style="background-color: #f1f5f9;">
                    <td class="value">التاريخ</td>
                    <td class="value">نوع المخالفة</td>
                    <td class="value">الإجراء المتخذ</td>
                </tr>
                @foreach ($student->violations as $violation)
                    <tr>
                        <td>{{ $violation->violation_date->format('Y-m-d') }}</td>
                        <td>{{ $violation->type }}</td>
                        <td>{{ $violation->penalty ? 'تمت العقوبة' : 'بانتظار الإجراء' }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <div class="footer">
        تم استخراج هذا الملف آلياً من نظام إدارة السكن الجامعي - {{ date('Y-m-d H:i') }}
    </div>

</body>

</html>
