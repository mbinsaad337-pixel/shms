<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: 'sans-serif';
            direction: rtl;
            text-align: right;
            font-size: 11px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f8fafc;
            color: #0f172a;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: left;
            font-size: 10px;
            color: #94a3b8;
        }

        .status-active { color: #16a34a; font-weight: bold; }
        .status-expired { color: #94a3b8; }
    </style>
</head>

<body>
    <!-- HEADER -->
    @include('partials.pdf_header', [
        'title' => 'سجل العقوبات الانضباطية المسندة',
        'number' => 'PEN-LIST-' . date('Ymd'),
        'department' => 'إدارة الإسكان وشؤون الطلاب'
    ])

    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 20%">الطالب</th>
                <th style="width: 20%">نوع العقوبة</th>
                <th style="width: 25%">الوصف</th>
                <th style="width: 15%">الفترة</th>
                <th style="width: 15%">الحالة</th>
            </tr>
        </thead>
        <tbody>
            @php
                $penaltyTypes = [
                    'verbal_warning' => 'تنبيه شفوي',
                    'written_warning' => 'إنذار كتابي',
                    'service_suspension' => 'حرمان من الخدمات',
                    'temporary_suspension' => 'فصل مؤقت',
                    'expulsion' => 'فصل نهائي'
                ];
            @endphp
            @foreach($penalties as $index => $penalty)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: right;">{{ $penalty->student?->name_ar ?? 'طالب غير موجود' }}</td>
                    <td>{{ $penaltyTypes[$penalty->type] ?? $penalty->type }}</td>
                    <td style="text-align: right; font-size: 10px;">{{ $penalty->description }}</td>
                    <td>
                        {{ $penalty->start_date?->format('Y/m/d') ?? '---' }}
                        إلى
                        {{ $penalty->end_date?->format('Y/m/d') ?? 'مفتوح' }}
                    </td>
                    <td class="{{ $penalty->is_active ? 'status-active' : 'status-expired' }}">
                        @if($penalty->is_active) سارية @else منتهية @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        طبع بواسطة: {{ auth()->user()->name }} | الصفحة {PAGENO} من {nbpg} | تاريخ الطباعة: {{ date('Y-m-d H:i') }}
    </div>
</body>

</html>
