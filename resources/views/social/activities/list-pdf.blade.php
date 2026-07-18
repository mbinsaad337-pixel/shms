<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&family=Almarai:wght@400;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; direction: rtl; text-align: right; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px; font-size: 13px; text-align: center; }
        th { background-color: #004274; color: white; }
        .footer { margin-top: 30px; text-align: left; font-size: 12px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    @include('partials.pdf_header', [
        'title' => 'تقرير سجل الأنشطة والفعاليات',
        'number' => 'ACT-REP-' . date('YmdHi'),
        'department' => $centerName
    ])

    <table>
        <thead>
            <tr>
                <th>الفعالية</th>
                <th>النادي المنظم</th>
                <th>التاريخ</th>
                <th>الموقع</th>
                <th>المشاركون</th>
                <th>المستهدفون</th>
               
            </tr>
        </thead>
        <tbody>
            @foreach($activities as $activity)
                <tr>
                    <td>{{ $activity->name }}</td>
                    <td>{{ $activity->club->name }}</td>
                    <td>{{ $activity->start_date->format('Y-m-d') }}</td>
                    <td>{{ $activity->location }}</td>
                    <td>{{ $activity->participants->count() }}</td>
                    <td>{{ $activity->targetedStudents()->count() }}</td>
                  
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        إجمالي الفعاليات: {{ $activities->count() }} | متوسط المشاركة: {{ $activities->count() > 0 ? round($activities->sum(fn($a) => $a->participants->count()) / $activities->count(), 1) : 0 }}
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
