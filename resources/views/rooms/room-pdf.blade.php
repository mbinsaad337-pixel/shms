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
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #e2e8f0;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #004274;
            color: white;
            font-weight: bold;
        }

        .label {
            font-weight: bold;
            color: #64748b;
        }

        .footer {
            margin-top: 50px;
            text-align: left;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    @include('partials.pdf_header', [
        'title' => 'كشف التسكين للمرفق السكني',
        'number' => 'ROOM-' . $room->room_number,
        'department' => 'إدارة الإسكان والمرافق'
    ])

    <div class="section">
        <h3 class="section-title">تفاصيل المرفق / الغرفة</h3>
        <table border="0" style="border:none;">
            <tr>
                <td style="border:none; text-align: right; padding: 5px;">
                    <span class="label">رقم الغرفة:</span>
                    <span style="font-weight: bold; color: #004274;">{{ $room->room_number }}</span>
                </td>
                <td style="border:none; text-align: right; padding: 5px;">
                    <span class="label">نوع الغرفة:</span>
                    <span>{{ $room->building == 'academic' ? 'أكاديمي' : ($room->building == 'cooperative' ? 'تعاوني' : ($room->building ?? '—')) }}</span>
                </td>
                <td style="border:none; text-align: right; padding: 5px;">
                    <span class="label">السعة:</span>
                    <span>{{ $room->capacity }} مقاعد</span>
                </td>
            </tr>
            <tr>
                <td style="border:none; text-align: right; padding: 5px;">
                    <span class="label">الطابق:</span>
                    <span>{{ $room->floor }}</span>
                </td>
                <td style="border:none; text-align: right; padding: 5px;">
                    <span class="label">الشقة:</span>
                    <span>{{ $room->apartment ?? 'بدون' }}</span>
                </td>
                <td style="border:none; text-align: right; padding: 5px;">
                    <span class="label">نسبة الإشغال:</span>
                    <span>{{ $room->assignments->count() }} / {{ $room->capacity }}</span>
                </td>
            </tr>
        </table>
    </div>

    <h4 style="color: #004274; border-right: 4px solid #004274; padding-right: 10px; margin-bottom: 15px;">قائمة الطلاب المسكنين في هذا المرفق:</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 35%">اسم الطالب</th>
                <th style="width: 20%">الرقم الجامعي</th>
                <th style="width: 20%">تاريخ التسكين</th>
                <th style="width: 20%">الحالة</th>
            </tr>
        </thead>
        <tbody>
            @if($room->assignments->count() > 0)
                @foreach($room->assignments as $index => $assignment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align: right;">{{ $assignment->student->name_ar }}</td>
                        <td>{{ $assignment->student->student_number }}</td>
                        <td>{{ $assignment->created_at->format('Y-m-d') }}</td>
                        <td style="color: #16a34a; font-weight: bold;">ساكن حالياً</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" style="padding: 30px; color: #94a3b8; font-style: italic;">لا يوجد طلاب مسكنين حالياً في هذا المرفق</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div style="margin-top: 50px;">
        <table style="width: 100%; border:none;">
            <tr>
                <td style="width: 50%; border:none; text-align: center;">
                    <p style="font-weight: bold;">توقيع مشرف الإسكان</p>
                    <br><br>
                    <p>............................</p>
                </td>
                <td style="width: 50%; border:none; text-align: center;">
                    <p style="font-weight: bold;">ختم الإدارة</p>
                    <br><br>
                    <p>............................</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        طبع بواسطة: {{ auth()->user()->name }} | الصفحة {PAGENO} من {nbpg} | تاريخ الطباعة: {{ date('Y-m-d H:i') }}
    </div>
</body>

</html>
