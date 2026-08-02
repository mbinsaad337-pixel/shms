@extends('pdf.layouts.master')

@section('content')

<div class="detail-card">
    <div class="detail-card-header">ملخص كشف المورد</div>
    <div class="detail-card-body">
        <table class="two-col-table">
            <tr>
                <td class="col-right">
                    <div class="detail-row">
                        <div class="detail-label">اسم المورد</div>
                        <div class="detail-value text-navy font-bold">{{ $supplier->name }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">المركز</div>
                        <div class="detail-value">{{ $supplier->center->name ?? '---' }}</div>
                    </div>
                </td>
                <td class="col-spacer"></td>
                <td class="col-left">
                    <div class="detail-row">
                        <div class="detail-label">رقم الهاتف</div>
                        <div class="detail-value  " dir="ltr">{{ $supplier->phone }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">الرصيد المتبقي (له)</div>
                        <div class="detail-value   text-danger font-bold text-lg">{{ number_format($supplier->balance, 2) }} ر.ي</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<h3 style="font-size: 14px; color: #004274; margin: 20px 0 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">كشف الفواتير</h3>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>رقم الفاتورة</th>
            <th class="text-center">التاريخ</th>
            <th class="text-center">المبلغ الكلي (ر.ي)</th>
            <th class="text-center">المدفوع (ر.ي)</th>
            <th class="text-center">المتبقي (ر.ي)</th>
            <th class="text-center">الحالة</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalInvoices = 0;
            $totalPaid = 0;
        @endphp
        @foreach($supplier->invoices as $index => $invoice)
        @php
            $totalInvoices += $invoice->total_amount;
            $totalPaid += $invoice->paid_amount;
        @endphp
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="  text-navy">{{ $invoice->invoice_number }}</td>
            <td class="text-center   text-sm">{{ $invoice->invoice_date instanceof \Carbon\Carbon ? $invoice->invoice_date->format('Y-m-d') : $invoice->invoice_date }}</td>
            <td class="text-center   font-bold">{{ number_format($invoice->total_amount, 2) }}</td>
            <td class="text-center   text-success">{{ number_format($invoice->paid_amount, 2) }}</td>
            <td class="text-center   text-danger">{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}</td>
            <td class="text-center">
                @php
                    $statusMap = [
                        'pending' => ['غير مدفوعة', 'badge-warning'],
                        'paid' => ['مدفوعة', 'badge-success'],
                        'partially_paid' => ['مدفوعة جزئياً', 'badge-info'],
                    ];
                    $status = $statusMap[$invoice->status] ?? [$invoice->status, 'badge-secondary'];
                @endphp
                <span class="badge {{ $status[1] }}">{{ $status[0] }}</span>
            </td>
        </tr>
        @endforeach
        
        @if($supplier->invoices->count() == 0)
        <tr>
            <td colspan="7" class="text-center text-muted" style="padding: 20px;">لا توجد فواتير مسجلة لهذا المورد</td>
        </tr>
        @endif
    </tbody>
    @if($supplier->invoices->count() > 0)
    <tfoot>
        <tr style="background: #fafbfc; font-weight: bold;">
            <td colspan="3" class="text-left">الإجمالي:</td>
            <td class="text-center  ">{{ number_format($totalInvoices, 2) }}</td>
            <td class="text-center   text-success">{{ number_format($totalPaid, 2) }}</td>
            <td class="text-center   text-danger">{{ number_format($totalInvoices - $totalPaid, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<table class="signatures-table avoid-break">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">أعده / مسؤول المشتريات</div>
            <div class="sign-name">{{ $exportUser ?? '' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">راجعه / المسؤول المالي</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">اعتمده / مدير المركز</div>
        </td>
    </tr>
</table>
@endsection
