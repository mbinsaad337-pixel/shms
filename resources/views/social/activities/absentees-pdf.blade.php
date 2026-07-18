<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&family=Almarai:wght@400;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; direction: rtl; text-align: right; margin: 20px; }
        .activity-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #dee2e6; padding: 10px; font-size: 13px; text-align: center; }
        th { background-color: #004274; color: white; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .footer { margin-top: 30px; text-align: left; font-size: 12px; }
        .summary { margin-top: 20px; font-weight: bold; color: #d9534f; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    @include('partials.pdf_header', [
        'title' => 'تقرير غياب الطلاب عن الفعالية',
        'number' => 'ABS-REP-' . date('YmdHi'),
        'department' => $activity->club->name ?? 'نادي عام'
    ])

    <div class="activity-info">
        <strong>اسم الفعالية:</strong> {{ $activity->name }}<br>
        <strong>التاريخ:</strong> {{ $activity->start_date?->format('Y-m-d') }}<br>
        <strong>الموقع:</strong> {{ $activity->location }}<br>
        <strong>النادي:</strong> {{ $activity->club->name ?? 'نادي عام' }}
    </div>

    <div class="summary">
        إجمالي عدد الغائبين: {{ $absentees->count() }} طالب من أصل {{ $activity->targetedStudents->count() }} مستهدف.
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%">#</th>
                <th style="width: 60%">اسم الطالب</th>
                <th style="width: 30%">الرقم الجامعي</th>
            </tr>
        </thead>
        <tbody>
            @if($absentees->count() > 0)
                @foreach($absentees as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->name_ar }}</td>
                        <td>{{ $student->student_number }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" style="text-align: center;">لا يوجد غياب حالياً.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        تم استخراج هذا التقرير بتاريخ: {{ now()->format('Y-m-d H:i') }}
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
