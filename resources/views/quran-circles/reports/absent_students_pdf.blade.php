<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير غياب الحلقات القرآنية</title>
    <style>
        body {
            font-family: 'dejavusans', sans-serif;
            direction: rtl;
            text-align: right;
            color: #1e293b;
            font-size: 13px;
            line-height: 1.6;
        }
        
        table { width: 100%; border-collapse: collapse; }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #112a6f;
            margin-bottom: 10px;
            border-bottom: 2px solid #cc8a0e;
            padding-bottom: 5px;
        }

        .data-table th {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 10px;
            font-size: 12px;
            color: #112a6f;
        }

        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 10px;
            font-size: 11px;
        }

        .text-center { text-align: center; }
        .text-danger { color: #e11d48; font-weight: bold; }

        .signatures-grid { margin-top: 50px; }
        .signatures-grid td { width: 33%; text-align: center; padding: 20px 10px; }
        .sign-line { border-top: 1px dashed #cbd5e1; margin: 30px 15px 5px; }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 10px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    @include('partials.pdf_header', [
        'title' => 'تقرير غياب طلاب الحلقات',
        'department' => 'قسم شؤون الحلقات والطلاب',
        'number' => 'QR-ABS-' . date('Ymd-Hi')
    ])

    <div class="section-title">بيانات الطلاب الغائبين بالمركز</div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="30%">اسم الطالب</th>
                <th width="20%">الحلقة</th>
                <th width="15%">تاريخ الجلسة</th>
                <th width="20%">رقم الهاتف</th>
                <th width="10%">الحالة</th>
            </tr>
        </thead>
        <tbody>
            @if($absences->count() > 0)
                @foreach($absences as $index => $absence)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $absence->student->name_ar }}</td>
                    <td>{{ $absence->session->circle->name }}</td>
                    <td class="text-center">{{ $absence->session->session_date->format('Y-m-d') }}</td>
                    <td class="text-center">{{ $absence->student->personal_phone ?? '---' }}</td>
                    <td class="text-center text-danger">غائب</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center" style="padding: 40px; color: #64748b;">
                        لا توجد سجلات غياب للمرشح المحدد.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="signatures-grid">
        <tr>
            <td>
                <strong>المدرس المسؤول</strong>
                <div class="sign-line"></div>
                <span style="font-size: 10px; color: #64748b;">التوقيع</span>
            </td>
            <td>
                <strong>مشرف الطلاب</strong>
                <div class="sign-line"></div>
                <span style="font-size: 10px; color: #64748b;">التوقيع</span>
            </td>
            <td>
                <strong>مدير المركز</strong>
                <div class="sign-line"></div>
                <span style="font-size: 10px; color: #64748b;">التوقيع والختم</span>
            </td>
        </tr>
    </table>

    <div class="footer">
        هذا التقرير مُستخرج من نظام إدارة السكنات الطلابية - جمعية رعاية طالب العلم <br>
        تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}
    </div>

</body>
</html>
