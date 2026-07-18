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

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 20px;
            margin: 0;
            color: #0f172a;
        }

        .header p {
            margin: 5px 0;
            color: #64748b;
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

        .status-residing {
            color: #16a34a;
            font-weight: bold;
        }

        .status-registered {
            color: #2563eb;
            font-weight: bold;
        }

        .status-left {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    @include('partials.pdf_header', [
        'title' => 'سجل الطلاب (جديد)',
        'number' => 'STU-LIST-' . date('Ymd'),
        'department' => 'إدارة الإسكان وشؤون الطلاب'
    ])

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>اسم الطالب</th>
                <th>الرقم الجامعي</th>
                <th>الهوية الوطنية</th>
                <th>المركز</th>
                <th>الجامعة</th>
                <th>التخصص</th>
                <th>{{ auth()->user()->hasRole('super-admin') ? 'السكن' : 'الحالة' }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: right;">{{ $student?->name_ar ?? 'غير معروف' }}</td>
                    <td>{{ $student?->student_number }}</td>
                    <td>{{ $student?->national_id }}</td>
                    <td>{{ $student?->center?->name ?? '-' }}</td>
                    <td>{{ $student?->university }}</td>
                    <td>{{ $student?->major }}</td>
                    <td>
                        @if(auth()->user()->hasRole('super-admin'))
                            {{ $student?->center?->name ?? 'غير محدد' }}
                        @else
                            <span class="status-{{ $student?->status }}">
                                @if($student?->status == 'residing') 
                                    مقيم
                                @elseif($student?->status == 'registered')
                                    {{ $student?->is_profile_approved ? 'تم الحجز' : 'حجز مبدئي' }}
                                @else 
                                    غادر 
                                @endif
                            </span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        طبع بواسطة: {{ auth()->user()->name }} | الصفحة {PAGENO} من {nbpg}
    </div>
</body>

</html>
