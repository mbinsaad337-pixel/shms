<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&family=Almarai:wght@400;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 9px 8px;
            font-size: 12px;
            text-align: center;
        }

        th {
            background-color: #004274;
            color: white;
        }

        .footer {
            margin-top: 20px;
            font-size: 13px;
        }

        .summary-box {
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 10px;
            padding: 12px 20px;
            display: inline-block;
            margin-top: 16px;
        }

        .summary-box .label {
            color: #166534;
            font-weight: bold;
            font-size: 13px;
        }

        .summary-box .amount {
            color: #15803d;
            font-size: 18px;
            font-weight: 900;
        }

        .cost-cell {
            color: #15803d;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    @include('partials.pdf_header', [
        'title' => 'تقرير سجل الأنشطة والفعاليات',
        'number' => 'ACT-REP-' . date('YmdHi'),
        'department' => $centerName,
    ])

    <table>
        <thead>
            <tr>
                <th>الفعالية</th>
                <th>الفئة</th>
                <th>النادي المنظم</th>
                <th>التاريخ</th>
                <th>الموقع</th>
                <th>المشاركون</th>
                <th>المستهدفون</th>
                <th>إجمالي التكلفة ( {{ currency_symbol() }} )</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($activities as $activity)
                <tr>
                    <td style="text-align:right">{{ $activity->name }}</td>
                    <td>{{ $activity->category ?? '—' }}</td>
                    <td>{{ $activity->club->name ?? '—' }}</td>
                    <td>{{ $activity->start_date->format('Y-m-d') }}</td>
                    <td>{{ $activity->location }}</td>
                    <td>{{ $activity->participants->count() }}</td>
                    <td>{{ $activity->targetedStudents()->count() }}</td>
                    <td class="cost-cell">{{ $activity->total_cost ? number_format($activity->total_cost, 2) : '—' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>إجمالي الفعاليات: <strong>{{ $activities->count() }}</strong>
            &nbsp;|&nbsp; إجمالي المشاركين: <strong>{{ $activities->sum(fn($a) => $a->participants->count()) }}</strong>
        </div>

        @if (isset($totalCost) && $totalCost > 0)
            <div class="summary-box">
                <span class="label">إجمالي التكلفة الكلية للفعاليات المعروضة:&nbsp;</span>
                <span class="amount">{{ number_format($totalCost, 2) }} {{ currency_symbol() }}</span>
            </div>
        @endif
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background-color: #004274; color: white; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: bold;">
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
