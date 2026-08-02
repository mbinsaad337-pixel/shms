@extends('pdf.layouts.master')

@section('content')
    @php
        $statusMap = [
            'draft' => ['مسودة', 'badge-secondary'],
            'submitted' => ['بانتظار الاعتماد', 'badge-warning'],
            'approved' => ['معتمدة', 'badge-success'],
            'rejected' => ['مرفوضة', 'badge-danger'],
        ];
        $status = $statusMap[$settlement->status] ?? [$settlement->status, 'badge-secondary'];
    @endphp

    <div class="detail-card">
        <div class="detail-card-header">ملخص التصفية المالية لقسم التغذية</div>
        <div class="detail-card-body">
            <table class="two-col-table">
                <tr>
                    <td class="col-right">
                        <div class="detail-row">
                            <div class="detail-label">الفترة</div>
                            <div class="detail-value  ">{{ $settlement->month_name }} {{ $settlement->year }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">تاريخ إعداد التصفية</div>
                            <div class="detail-value   text-sm">{{ $settlement->created_at->format('Y-m-d') }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">أعدت بواسطة</div>
                            <div class="detail-value">{{ $settlement->creator?->name ?? '-' }}</div>
                        </div>
                    </td>
                    <td class="col-spacer"></td>
                    <td class="col-left">
                        <div class="detail-row">
                            <div class="detail-label">الحالة</div>
                            <div class="detail-value"><span class="badge {{ $status[1] }}">{{ $status[0] }}</span></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">تاريخ الاعتماد</div>
                            <div class="detail-value   text-sm">{{ $settlement->approved_at?->format('Y-m-d H:i') ?? 'لم تعتمد بعد' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">النتيجة النهائية</div>
                            <div class="detail-value   {{ $settlement->net_result >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format(abs($settlement->net_result), 2) }} ر.ي ({{ $settlement->getResultTypeLabel() }})</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <table class="stats-row" style="margin-bottom: 25px;">
        <tr>
            <td class="stat-card" style="border-bottom: 3px solid #15803d;">
                <div class="stat-label">إجمالي المقبوضات (الإيرادات)</div>
                <div class="stat-value text-success">{{ number_format($settlement->total_revenue, 2) }} <span class="stat-unit">ر.ي</span></div>
            </td>
            <td class="stat-card" style="border-bottom: 3px solid #b91c1c;">
                <div class="stat-label">إجمالي المصروفات التشغيلية</div>
                <div class="stat-value text-danger">{{ number_format($settlement->total_expenses, 2) }} <span class="stat-unit">ر.ي</span></div>
            </td>
            <td class="stat-card" style="border-bottom: 3px solid #4f46e5;">
                <div class="stat-label">إجمالي مديونية الموردين</div>
                <div class="stat-value text-navy">{{ number_format($settlement->total_debt, 2) }} <span class="stat-unit">ر.ي</span></div>
            </td>
        </tr>
    </table>

    <h3 style="font-size: 14px; color: #004274; margin: 20px 0 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">تفاصيل الإيرادات والمقبوضات</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">م</th>
                <th>رقم السند</th>
                <th>الطالب / المودع</th>
                <th class="text-center">التاريخ</th>
                <th>البيان</th>
                <th class="text-center">المبلغ (ر.ي)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receipts as $index => $receipt)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold  ">{{ $receipt->voucher_number }}</td>
                    <td>{{ $receipt->student?->name_ar ?? '-' }}</td>
                    <td class="text-center  ">{{ $receipt->voucher_date->format('Y-m-d') }}</td>
                    <td>{{ $receipt->description ?? '-' }}</td>
                    <td class="text-center   text-success">{{ number_format($receipt->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">لا توجد سجلات مقبوضات لهذه الفترة.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-left">إجمالي المقبوضات:</th>
                <th class="text-center   text-success">{{ number_format($receipts->sum('amount'), 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <h3 style="font-size: 14px; color: #004274; margin: 20px 0 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">تفاصيل فواتير الشراء</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">م</th>
                <th>رقم الفاتورة</th>
                <th>المورد</th>
                <th class="text-center">طريقة الدفع</th>
                <th class="text-center">تاريخ الفاتورة</th>
                <th class="text-center">المبلغ (ر.ي)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $index => $invoice)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold  ">{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->supplier?->name ?? '-' }}</td>
                    <td class="text-center">{{ $invoice->payment_type_label }}</td>
                    <td class="text-center  ">{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                    <td class="text-center   text-danger">{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">لا توجد فواتير شراء لهذه الفترة.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="font-size: 14px; color: #004274; margin: 20px 0 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">تفاصيل سندات الصرف</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">م</th>
                <th>رقم السند</th>
                <th>المورد</th>
                <th class="text-center">التاريخ</th>
                <th>البيان</th>
                <th class="text-center">المبلغ (ر.ي)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $index => $payment)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold  ">{{ $payment->voucher_number }}</td>
                    <td>{{ $payment->supplier?->name ?? '-' }}</td>
                    <td class="text-center  ">{{ $payment->voucher_date->format('Y-m-d') }}</td>
                    <td>{{ $payment->description ?? '-' }}</td>
                    <td class="text-center   text-danger">{{ number_format($payment->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">لا توجد سندات صرف لهذه الفترة.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-left">إجمالي المصروفات التشغيلية:</th>
                <th class="text-center   text-danger">{{ number_format($payments->sum('amount'), 2) }}</th>
            </tr>
        </tfoot>
    </table>

    @if($settlement->notes)
        <div class="detail-card avoid-break" style="margin-top: 20px;">
            <div class="detail-card-header">ملاحظات</div>
            <div class="detail-card-body"><p style="font-size: 11px;">{{ $settlement->notes }}</p></div>
        </div>
    @endif

    <table class="signatures-table avoid-break">
        <tr>
            <td><div class="sign-line"></div><div class="sign-title">أعده / مسؤول التغذية</div><div class="sign-name">{{ $settlement->creator?->name ?? '-' }}</div></td>
            <td><div class="sign-line"></div><div class="sign-title">راجعه / مدير المركز</div><div class="sign-name">{{ $settlement->approver?->name ?? '-' }}</div></td>
            <td><div class="sign-line"></div><div class="sign-title">مصادقة الإدارة المركزية</div></td>
        </tr>
    </table>
@endsection
