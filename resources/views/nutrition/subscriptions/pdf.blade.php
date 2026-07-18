<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-size: 14px;
            direction: rtl;
            text-align: right;
            color: #333;
            line-height: 1.6;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table th {
            background-color: #f1f5f9;
            padding: 10px;
            font-weight: bold;
            border: 1px solid #cbd5e0;
            text-align: center;
        }

        .data-table td {
            padding: 8px 10px;
            border: 1px solid #cbd5e0;
            text-align: center;
        }

        .text-right {
            text-align: right !important;
        }

        .text-left {
            text-align: left !important;
        }

        .balance-positive {
            color: #16a34a;
            font-weight: bold;
        }

        .balance-negative {
            color: #dc2626;
            font-weight: bold;
        }

        .footer-signatures {
            margin-top: 50px;
            width: 100%;
        }

        .footer-signatures td {
            width: 50%;
            text-align: center;
        }
    </style>
</head>
<body>

    @include('partials.pdf_header', [
        'title' => 'قائمة المشتركين في التغذية',
        'number' => 'NUT-SUB-' . date('YmdHi'),
        'department' => 'قسم التغذية - ' . (auth()->user()->center->name ?? 'المركز')
    ])

    <h3 style="text-align: center; margin-top: 20px;">الطلاب المشتركون النشطين والمسجلين</h3>

    <table class="data-table">
        <thead>
            <tr>
                <th>م</th>
                <th>اسم الطالب</th>
                <th>نوع الاشتراك</th>
                <th>إجمالي المستحق</th>
                <th>إجمالي المسدد</th>
                <th>الرصيد المتبقي</th>
            </tr>
        </thead>
        <tbody>
            @if ($subscriptions->count() > 0)
                @foreach ($subscriptions as $index => $sub)
                    @php $balance = $sub->total_paid - $sub->total_due; @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-right">{{ $sub->student->name_ar ?? '---' }}</td>
                        <td>{{ $sub->getTypeLabel() }}</td>
                        <td class="balance-negative">{{ number_format($sub->total_due, 2) }}</td>
                        <td class="balance-positive">{{ number_format($sub->total_paid, 2) }}</td>
                        <td class="{{ $balance >= 0 ? 'balance-positive' : 'balance-negative' }}">
                            {{ number_format(abs($balance), 2) }} {{ $balance >= 0 ? '(دائن)' : '(مدين)' }}
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" style="padding: 20px; color: #718096;">لا يوجد مشتركون في هذه القائمة</td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="footer-signatures">
        <tr>
            <td>
                <strong>إعداد الموظف</strong><br><br>
                <span>......................</span>
            </td>
            <td>
                <strong>اعتماد مدير التغذية</strong><br><br>
                <span>......................</span>
            </td>
        </tr>
    </table>

</body>
</html>
