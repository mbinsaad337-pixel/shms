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

        .severity-minor { color: #2563eb; font-weight: bold; }
        .severity-moderate { color: #d97706; font-weight: bold; }
        .severity-severe { color: #dc2626; font-weight: bold; }
    </style>
</head>

<body>
    <!-- HEADER -->
    @include('partials.pdf_header', [
        'title' => 'سجل المخالفات الانضباطية',
        'number' => 'VIO-LIST-' . date('Ymd'),
        'department' => 'إدارة شؤون الطلاب'
    ])

    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 20%">الطالب</th>
                <th style="width: 15%">نوع المخالفة</th>
                <th style="width: 35%">الوصف</th>
                <th style="width: 10%">المستوى</th>
                <th style="width: 15%">التاريخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($violations as $index => $violation)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: right;">{{ $violation->student?->name_ar ?? 'طالب غير موجود' }}</td>
                    <td>{{ $violation->type }}</td>
                    <td style="text-align: right; font-size: 10px;">{{ $violation->description }}</td>
                    <td class="severity-{{ $violation->severity }}">
                        @if($violation->severity == 'minor') بسيطة
                        @elseif($violation->severity == 'moderate') متوسطة
                        @else جسيمة @endif
                    </td>
                    <td>{{ $violation->violation_date->format('Y/m/d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        طبع بواسطة: {{ auth()->user()->name }} | الصفحة {PAGENO} من {nbpg} | تاريخ الطباعة: {{ date('Y-m-d H:i') }}
    </div>
</body>

</html>
