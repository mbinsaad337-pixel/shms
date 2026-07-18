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
        'title' => 'قائمة الأصول الثابتة',
        'number' => 'AST-REP-' . date('YmdHi'),
        'department' => $centerName
    ])

    <table>
        <thead>
            <tr>
                <th style="width: 5%">م</th>
                <th style="width: 25%">اسم الأصل</th>
                <th style="width: 15%">الكود</th>
                <th style="width: 15%">التصنيف</th>
                <th style="width: 15%">النوع</th>
                <th style="width: 15%">المركز</th>
                <th style="width: 10%">الحالة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ $asset->name }}</td>
                    <td style="direction: ltr; font-weight: bold;">{{ $asset->code }}</td>
                    <td>{{ $asset->category }}</td>
                    <td>{{ $asset->type }}</td>
                    <td>{{ $asset->center->name ?? 'غير محدد' }}</td>
                    <td>
                        @switch($asset->status)
                            @case('good') ممتازة @break
                            @case('needs_maintenance') بحاجة لصيانة @break
                            @case('damaged') تالفة @break
                            @case('disposed') مُتلفة @break
                            @default {{ $asset->status }}
                        @endswitch
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        إجمالي الأصول: {{ $assets->count() }} 
        | جيدة: {{ $assets->where('status', 'good')->count() }}
        | صيانة: {{ $assets->where('status', 'needs_maintenance')->count() }}
        | تالفة: {{ $assets->where('status', 'damaged')->count() }}
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
