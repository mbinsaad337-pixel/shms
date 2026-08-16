<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>
        @php
            $titleStr = [
                'receipt' => 'سند قبض',
                'payment' => 'سند صرف',
                'transfer' => 'سند تحويل',
                'salary' => 'مسير رواتب',
            ][$voucher->type] ?? 'سند مالي';
        @endphp
        {{ $titleStr }} - {{ $voucher->voucher_number }}
    </title>
    <style>
        body {
            font-family: 'dejavusans', sans-serif;
            direction: rtl;
            text-align: right;
            color: #334155;
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        /* Top Summary Bar */
        .summary-wrapper {
            width: 100%;
            margin-bottom: 30px;
        }
        .summary-card {
            width: 24%;
            background-color: #ffffff;
            border: 1px solid #f1f5f9;
            padding: 15px 10px;
            text-align: center;
            vertical-align: top;
            border-radius: 15px;
        }
        .summary-card .label {
            color: #94a3b8;
            font-size: 11px;
            margin-bottom: 5px;
        }
        .summary-card .value {
            color: #1e293b;
            font-size: 14px;
            font-weight: bold;
        }
        .summary-card .amount {
            color: #1e293b;
            font-size: 18px;
            font-weight: 900;
        }

        /* Main Content Grid */
        .content-grid {
            width: 100%;
        }
        .column {
            width: 48%;
            vertical-align: top;
        }
        .spacer {
            width: 4%;
        }

        /* Cards */
        .card {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 0;
            margin-bottom: 20px;
            border: 1px solid #f1f5f9;
        }
        .card-header {
            padding: 12px 15px;
            border-bottom: 1px solid #f8fafc;
            color: #1e293b;
            font-weight: bold;
            font-size: 13px;
        }
        .card-body {
            padding: 15px;
        }

        /* Data List */
        .data-item {
            margin-bottom: 15px;
        }
        .data-item .data-label {
            color: #64748b;
            font-size: 11px;
            margin-bottom: 3px;
        }
        .data-item .data-value {
            color: #1e293b;
            font-weight: bold;
            font-size: 14px;
        }

        /* Student Card (Specific styling) */
        .student-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px;
            margin-top: 10px;
        }
        .student-name {
            color: #004274;
            font-weight: bold;
            font-size: 13px;
        }
        .remaining-label {
            color: #dc2626;
            font-size: 10px;
            margin-top: 5px;
        }

        /* Description Box */
        .desc-box {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            min-height: 80px;
            font-size: 12px;
            color: #475569;
        }

        /* Status Badge */
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-approved { background-color: #dcfce7; color: #15803d; }
        .badge-pending { background-color: #fef9c3; color: #854d0e; }
        .badge-rejected { background-color: #fee2e2; color: #b91c1c; }

    </style>
</head>
<body>

    @php
        $types = [
            'receipt' => 'سند قبض',
            'payment' => 'سند صرف',
            'transfer' => 'سند تحويل',
            'salary' => 'مسير رواتب',
        ];
        $typeLabel = $types[$voucher->type] ?? 'سند مالي';
    @endphp

    @include('partials.pdf_header', [
        'title' => $typeLabel,
        'number' => $voucher->voucher_number,
        'department' => $voucher->center->name ?? 'المركز السكني'
    ])

    <!-- Summary Bar -->
    <table class="summary-wrapper" cellpadding="0" cellspacing="0">
        <tr>
            <td class="summary-card">
                <div class="label">المبلغ الإجمالي</div>
                <div class="amount">{{ number_format($voucher->amount, 2) }} <small style="font-size: 10px">ر.ي</small></div>
            </td>
            <td style="width: 1%"></td>
            <td class="summary-card">
                <div class="label">طبيعة السند</div>
                <div class="value" style="color: #004274">{{ $typeLabel }} {{ $voucher->sub_type == 'housing' ? '(تسكين)' : ($voucher->sub_type == 'deposit' ? '(إيداع)' : '') }}</div>
            </td>
            <td style="width: 1%"></td>
            <td class="summary-card">
                <div class="label">الصندوق</div>
                <div class="value">{{ $voucher->fund->name ?? '-' }}</div>
            </td>
            <td style="width: 1%"></td>
            <td class="summary-card">
                <div class="label">المنشئ</div>
                <div class="value">{{ $voucher->creator->name ?? '-' }}</div>
            </td>
        </tr>
    </table>

    <table class="content-grid" cellpadding="0" cellspacing="0">
        <tr>
            <!-- RIGHT COLUMN -->
            <td class="column">
                <div class="card">
                    <div class="card-header">
                        <span style="color: #64748b; margin-left: 5px;">ℹ️</span> بيانات المستفيد أو الدافع
                    </div>
                    <div class="card-body">
                        <div class="data-item">
                            <div class="data-label">
                                @if(in_array($voucher->type, ['payment', 'salary']))يُصرف للسيد/الجهة:
                                @elseif($voucher->type == 'receipt')استلمنا من السيد/الجهة:
                                @else المستفيد/المودع:
                                @endif
                            </div>
                            <div class="data-value">{{ $voucher->payee_or_payer }}</div>
                        </div>

                        @if($voucher->student)
                        <div class="student-box">
                            <table style="width:100%">
                                <tr>
                                    <td>
                                        <div style="font-size: 9px; color: #94a3b8;">مرتبط بالطالب:</div>
                                        <div class="student-name">{{ $voucher->student->name_ar }}</div>
                                    </td>
                                    <td style="text-align: left;">
                                        <div class="remaining-label">المتبقي من الرسوم:</div>
                                        <div style="font-weight: bold; color: #dc2626; font-size: 12px;">{{ number_format($voucher->student->remaining_fees, 2) }} ر.ي</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <span style="color: #64748b; margin-left: 5px;">📄</span> البيان / التفاصيل
                    </div>
                    <div class="card-body">
                        <div class="desc-box">
                            {{ $voucher->description }}
                        </div>
                    </div>
                </div>
            </td>

            <td class="spacer"></td>

            <!-- LEFT COLUMN -->
            <td class="column">
                @if($voucher->type === 'transfer' && $voucher->targetFund)
                <div class="card" style="border: 1px solid #e0f2fe; background-color: #f0f9ff;">
                    <div class="card-header" style="color: #0369a1;">بيانات التحويل</div>
                    <div class="card-body">
                         <div style="font-size: 11px; color: #0c4a6e;">وجهة التحويل:</div>
                         <div style="font-weight: bold; color: #0369a1; font-size: 14px;">{{ $voucher->targetFund->name }}</div>
                    </div>
                </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <span style="color: #64748b; margin-left: 5px;">📅</span> تفاصيل إضافية
                    </div>
                    <div class="card-body">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 8px 0; color: #64748b;">المركز</td>
                                <td style="padding: 8px 0; text-align: left; font-weight: bold;">{{ $voucher->center->name }}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 8px 0; color: #64748b;">تاريخ السند</td>
                                <td style="padding: 8px 0; text-align: left; font-weight: bold;">{{ $voucher->date->format('Y-m-d') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>


            </td>
        </tr>
    </table>

    <!-- Footer Signatures -->
    <table style="width: 100%; margin-top: 40px; text-align: center;">
        <tr>
            <td style="width: 33%">
                <div style="border-top: 1px dashed #cbd5e1; margin: 30px 10px 10px; width: 80%; display: inline-block;"></div><br>
                <span style="font-weight: bold; font-size: 11px;">توقيع المنشئ</span><br>
                <small style="color: #64748b">{{ $voucher->creator->name ?? '-' }}</small>
            </td>
            <td style="width: 33%">
                 <div style="border-top: 1px dashed #cbd5e1; margin: 30px 10px 10px; width: 80%; display: inline-block;"></div><br>
                <span style="font-weight: bold; font-size: 11px;">المحاسب المالي</span>
            </td>
            <td style="width: 33%">
                 <div style="border-top: 1px dashed #cbd5e1; margin: 30px 10px 10px; width: 80%; display: inline-block;"></div><br>
                <span style="font-weight: bold; font-size: 11px;">اعتماد مدير المركز</span><br>
                <small style="color: #64748b">{{ $voucher->approver->name ?? '-' }}</small>
            </td>
        </tr>
    </table>

    <div style="position: absolute; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #94a3b8; padding-top: 10px; border-top: 1px solid #f1f5f9;">
        هذا السند مُنشأ آلياً عبر منصة ادارة  المراكز الطلابية- {{ date('Y-m-d H:i') }}
    </div>

</body>
</html>
