<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: 'sans-serif';
            direction: rtl;
            text-align: right;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }

        .section {
            margin-bottom: 25px;
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 15px;
            border: 1px solid #e2e8f0;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #004274;
            margin-bottom: 15px;
            border-bottom: 2px solid #D4A044;
            display: inline-block;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        td {
            padding: 10px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            color: #64748b;
            width: 25%;
        }

        .value {
            color: #0f172a;
            font-weight: 500;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }

        .severity-minor { color: #2563eb; }
        .severity-moderate { color: #d97706; }
        .severity-severe { color: #dc2626; }
    </style>
</head>

<body>
    <!-- HEADER -->
    @include('partials.pdf_header', [
        'title' => 'تقرير مخالفة انضباطية',
        'number' => 'VIO-' . $violation->id,
        'department' => 'إدارة شؤون الطلاب'
    ])

    <div class="section">
        <h3 class="section-title">بيانات الطالب</h3>
        <table>
            <tr>
                <td class="label">اسم الطالب:</td>
                <td class="value">{{ $violation->student->name_ar }}</td>
                <td class="label">الرقم الجامعي:</td>
                <td class="value">{{ $violation->student->student_number }}</td>
            </tr>
            <tr>
                <td class="label">الهوية الوطنية:</td>
                <td class="value">{{ $violation->student->national_id }}</td>
                <td class="label">المركز السكني:</td>
                <td class="value">{{ $violation->student->center->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3 class="section-title">تفاصيل المخالفة</h3>
        <table>
            <tr>
                <td class="label">نوع المخالفة:</td>
                <td class="value">{{ $violation->type }}</td>
                <td class="label">مستوى الجسامة:</td>
                <td class="value {{ 'severity-' . $violation->severity }}">
                    @if($violation->severity == 'minor') بسيطة
                    @elseif($violation->severity == 'moderate') متوسطة
                    @else جسيمة @endif
                </td>
            </tr>
            <tr>
                <td class="label">تاريخ المخالفة:</td>
                <td class="value">{{ $violation->violation_date->format('Y/m/d') }}</td>
                <td class="label">سجلت بواسطة:</td>
                <td class="value">{{ $violation->recordedBy->name ?? 'نظام مبرمج' }}</td>
            </tr>
        </table>
        <div style="margin-top: 15px; padding: 15px; background: white; border-radius: 10px; border: 1px solid #e2e8f0;">
            <p style="font-weight: bold; color: #64748b; margin-bottom: 8px;">وصف المخالفة:</p>
            <p style="margin: 0; line-height: 1.8;">{{ $violation->description }}</p>
        </div>
    </div>

    @if($violation->penalty)
    <div class="section" style="background-color: #fff1f2; border-color: #fecaca;">
        <h3 class="section-title" style="color: #be123c; border-bottom-color: #be123c;">العقوبة المسندة</h3>
        <table>
            <tr>
                <td class="label">نوع العقوبة:</td>
                <td class="value" style="color: #be123c; font-weight: 900;">
                    @php
                        $penaltyTypes = [
                            'verbal_warning' => 'تنبيه شفوي',
                            'written_warning' => 'إنذار كتابي',
                            'service_suspension' => 'حرمان من الخدمات المؤقت',
                            'temporary_suspension' => 'فصل مؤقت من السكن',
                            'expulsion' => 'فصل نهائي'
                        ];
                    @endphp
                    {{ $penaltyTypes[$violation->penalty->type] ?? $violation->penalty->type }}
                </td>
            </tr>
            <tr>
                <td class="label">الفترة:</td>
                <td class="value">
                    {{ $violation->penalty->start_date?->format('Y/m/d') ?? '---' }}
                    إلى
                    {{ $violation->penalty->end_date?->format('Y/m/d') ?? 'مفتوح' }}
                </td>
            </tr>
        </table>
        <div style="margin-top: 15px; padding: 15px; background: white; border-radius: 10px; border: 1px solid #fecaca;">
            <p style="font-weight: bold; color: #be123c; margin-bottom: 8px;">وصف العقوبة:</p>
            <p style="margin: 0; line-height: 1.8;">{{ $violation->penalty->description }}</p>
        </div>
    </div>
    @endif

    <div style="margin-top: 50px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 33.33%; text-align: center;">
                    <p style="font-weight: bold;">توقيع الطالب المعني</p>
                    <br><br>
                    <p>............................</p>
                </td>
                <td style="width: 33.33%; text-align: center;">
                    <p style="font-weight: bold;">توقيع مسجل المخالفة</p>
                    <br><br>
                    <p>............................</p>
                </td>
                <td style="width: 33.33%; text-align: center;">
                    <p style="font-weight: bold;">ختم الإدارة</p>
                    <br><br>
                    <p>............................</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        طبع بواسطة: {{ auth()->user()->name }} | تاريخ الطباعة: {{ date('Y-m-d H:i') }}
    </div>
</body>

</html>
