<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>عهدة شهر {{ $budget->month }} / {{ $budget->year }}</title>
    <style>
        body {
            font-family: 'dejavusans', sans-serif;
            direction: rtl;
            text-align: right;
            color: #1e293b;
            font-size: 14px;
            line-height: 1.6;
        }

        .header-table {
            width: 100%;
            background-color: #112a6f;
            color: white;
            margin-bottom: 25px;
        }

        .header-table td {
            padding: 20px;
        }

        .doc-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: white;
        }

        .doc-subtitle {
            font-size: 14px;
            color: #cbd5e1;
        }

        .system-name {
            font-size: 20px;
            font-weight: bold;
            color: #cc8a0e;
        }

        .info-tables-container {
            width: 100%;
            margin-bottom: 25px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table th {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px;
            font-size: 13px;
            color: #112a6f;
            text-align: right;
            width: 35%;
        }

        .info-table td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            font-size: 13px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #112a6f;
            margin-bottom: 10px;
            border-bottom: 2px solid #cc8a0e;
            padding-bottom: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #112a6f;
            color: white;
            padding: 10px;
            text-align: right;
            border: 1px solid #112a6f;
            font-size: 13px;
        }

        .items-table td {
            padding: 10px;
            border: 1px solid #cbd5e1;
            font-size: 13px;
        }

        .items-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .total-row td {
            background-color: #cc8a0e;
            color: white;
            font-weight: bold;
            border-color: #cc8a0e;
            font-size: 14px;
        }

        .notes-box {
            border: 1px solid #fde68a;
            background-color: #fffbeb;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 5px;
        }

        .signatures-table {
            width: 100%;
            text-align: center;
            margin-top: 50px;
        }

        .signatures-table td {
            width: 33.33%;
            padding: 10px;
        }

        .signature-line {
            border-top: 1px solid #cbd5e1;
            margin: 40px 20px 10px;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 11px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    @php /** @var \App\Models\MonthlyBudget $budget */ @endphp
    @include('partials.pdf_header', [
        'title' => 'طلب العهدة الشهرية',
        'number' => 'BUD-' . str_pad($budget->id, 5, '0', STR_PAD_LEFT),
        'department' => 'الإدارة المالية - ' . ($budget->center->name ?? 'المركز')
    ])

    <table class="info-tables-container" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 48%; vertical-align: top;">
                <div class="section-title">معلومات الطلب</div>
                <table class="info-table">
                    <tr>
                        <th>رقم الطلب</th>
                        <td>#{{ str_pad($budget->id, 5, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <th>الفترة</th>
                        <td>{{ $budget->month }} / {{ $budget->year }}</td>
                    </tr>
                    <tr>
                        <th>بواسطة</th>
                        <td>{{ $budget->submitter->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>الحالة</th>
                        <td style="font-weight:bold; color: #112a6f;">
                            @php
                                $statusLabels = [
                                    'draft' => 'مسودة',
                                    'submitted' => 'قيد مراجعة المدير',
                                    'confirmed' => 'بانتظار مدير قسم المراكز الطلابية',
                                    'approved' => 'تم الاعتماد النهائي',
                                    'rejected' => 'مرفوض',
                                ];
                            @endphp
                            {{ $statusLabels[$budget->status] ?? $budget->status }}
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%; vertical-align: top;">
                <div class="section-title">ملخص مالي</div>
                <table class="info-table">
                    <tr>
                        <th>المركز</th>
                        <td>{{ $budget->center->name }}</td>
                    </tr>
                    <tr>
                        <th>عدد البنود</th>
                        <td>{{ $budget->items->count() }} بند</td>
                    </tr>
                    <tr>
                        <th style="color:#112a6f;">إجمالي المطلوب</th>
                        <td style="color:#112a6f; font-weight:bold;">{{ number_format($budget->total_amount, 2) }} {{ currency_symbol() }}
                        </td>
                    </tr>
                    @if($budget->status === 'approved')
                        <tr>
                            <th>المعتمد من</th>
                            <td style="color:#166534; font-weight:bold;">{{ $budget->approver->name ?? '-' }}</td>
                        </tr>
                    @else
                        <tr>
                            <th>المعتمد من</th>
                            <td style="color:#94a3b8;">لم يُعتمد بعد</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">بنود العهدة الشهرية التفصيلية</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>الصندوق / الجهة</th>
                <th>المبلغ المطلوب</th>
                <th>الرصيد الحالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($budget->items as $index => $item)
                @php /** @var \App\Models\BudgetItem $item */ @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->fund->name }} <span style="color:#047857; font-size:11px;">({{ $item->fund->currency_label }})</span></td>
                    <td style="font-weight:bold; color:#112a6f;">{{ number_format($item->requested_amount, 2) }} {{ $item->fund->currency_symbol }}</td>
                    <td>{{ number_format($item->fund->balance, 2) }} {{ $item->fund->currency_symbol }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">الإجمالي الكلي</td>
                <td>{{ number_format($budget->total_amount, 2) }} {{ currency_symbol() }}</td>
                <td>-</td>
            </tr>
        </tbody>
    </table>

    @if($budget->notes)
        <div class="section-title">ملاحظات</div>
        <div class="notes-box">
            {{ $budget->notes }}
        </div>
    @endif

    <table class="signatures-table">
        <tr>
            <td>
                <div class="signature-line"></div>
                <strong>مقدِّم الطلب (المالية)</strong><br>
                {{ $budget->submitter->name ?? '-' }}
            </td>
            <td>
                <div class="signature-line"></div>
                <strong>مدير المركز</strong><br>
                <span>&nbsp;</span>
            </td>
            <td>
                <div class="signature-line"></div>
                <strong>مدير قسم المراكز الطلابية / المعتمد</strong><br>
                {{ $budget->approver->name ?? '-' }}
            </td>
        </tr>
    </table>

    <div class="footer">
        تم الإنشاء تلقائياً من منصة إدارة المراكز الطلابية - جمعية رعاية طالب العلم | تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}
    </div>

</body>

</html>
