<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
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

        .status-available { color: #16a34a; font-weight: bold; }
        .status-maintenance { color: #d97706; font-weight: bold; }
        .status-closed { color: #dc2626; font-weight: bold; }
    </style>
</head>

<body>
    <!-- HEADER -->
    @include('partials.pdf_header', [
        'title' => 'تقرير جرد الغرف والمرافق السكنية',
        'number' => 'ROOM-LIST-' . date('Ymd'),
        'department' => 'إدارة الإسكان والمرافق'
    ])

    <table>
        <thead>
            <tr>
                <th>نوع الغرفة</th>
                <th>الطابق</th>
                <th>الشقة</th>
                <th>رقم الغرفة</th>
                <th>السعة القصوى</th>
                <th>عدد الساكنين</th>
                <th>المساحات الشاغرة</th>
                <th>حالة المرفق</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rooms as $room)
                <tr>
                    <td>{{ $room->building == 'academic' ? 'أكاديمي' : ($room->building == 'cooperative' ? 'تعاوني' : ($room->building ?? '—')) }}</td>
                    <td>{{ $room->floor }}</td>
                    <td>{{ $room->apartment ?? '-' }}</td>
                    <td style="font-weight: bold; color: #004274;">{{ $room->room_number }}</td>
                    <td>{{ $room->capacity }}</td>
                    <td>{{ $room->students_count }}</td>
                    <td style="background-color: {{ ($room->capacity - $room->students_count) > 0 ? '#f0fdf4' : '#fff1f2' }}">
                        {{ $room->capacity - $room->students_count }}
                    </td>
                    <td class="status-{{ $room->status }}">
                        @if($room->status == 'available') متاح
                        @elseif($room->status == 'maintenance') صيانة
                        @else مغلق/أخرى @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        إجمالي الغرف: {{ $rooms->count() }} | إجمالي الساكنين: {{ $rooms->sum('students_count') }} | 
        طبع بواسطة: {{ auth()->user()->name }} | الصفحة {PAGENO} من {nbpg}
    </div>
</body>

</html>
