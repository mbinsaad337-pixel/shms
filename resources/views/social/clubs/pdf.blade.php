<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&family=Almarai:wght@400;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; direction: rtl; text-align: right; color: #1e293b; font-size: 14px; line-height: 1.6; margin: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th { background-color: #f1f5f9; color: #004274; text-align: center; padding: 12px; border: 1px solid #e2e8f0; font-weight: bold; }
        .table td { padding: 10px; border: 1px solid #e2e8f0; text-align: center; }
        .footer { text-align: center; font-size: 10px; color: #94a3b8; margin-top: 40px; border-top: 1px solid #f1f5f9; padding-top: 10px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; color: white; background-color: #004274; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    @include('partials.pdf_header', [
        'title' => 'بيان أعضاء النادي - ' . $club->name,
        'number' => 'CLUB-' . $club->id,
        'department' => 'قسم الأنشطة الاجتماعية'
    ])

    <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 25px;">
        <p style="margin: 0; font-weight: bold; color: #004274;">معلومات النادي:</p>
        <table style="width: 100%; margin-top: 10px;">
            <tr>
                <td style="width: 50%; border:none; text-align:right;">اسم النادي: {{ $club->name }}</td>
                <td style="width: 50%; border:none; text-align:right;">التصنيف: {{ $club->category }}</td>
            </tr>
            <tr>
                <td style="width: 50%; border:none; text-align:right;">عدد الأعضاء: {{ $club->members->count() }}</td>
                <td style="width: 50%; border:none; text-align:right;">تاريخ التقرير: {{ date('Y/m/d') }}</td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>اسم الطالب</th>
                <th>الرقم الجامعي</th>
                <th>الدور</th>
                <th>تاريخ الانضمام</th>
            </tr>
        </thead>
        <tbody>
            @foreach($club->members as $index => $member)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $member->student?->name_ar ?? 'طالب غير موجود' }}</td>
                <td>{{ $member->student?->student_number ?? '-' }}</td>
                <td>{{ $member->role }}</td>
                <td>{{ $member->joined_at ? date('Y/m/d', strtotime($member->joined_at)) : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        هذا التقرير تم استخراجه من نظام إدارة طالب العلم الإكلينكي - {{ date('Y-m-d H:i') }}
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #004274; color: white; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: bold;">
            <i class="fas fa-print"></i> طباعة التقرير
        </button>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>

</body>
</html>
