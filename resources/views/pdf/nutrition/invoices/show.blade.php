@extends('pdf.layouts.master')

@section('content')

<div class="detail-card">
    <div class="detail-card-header">تفاصيل الفاتورة</div>
    <div class="detail-card-body">
        <table class="two-col-table">
            <tr>
                <td class="col-right">
                    <div class="detail-row">
                        <div class="detail-label">رقم الفاتورة</div>
                        <div class="detail-value   text-navy font-bold">{{ $invoice->invoice_number }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">المورد</div>
                        <div class="detail-value">{{ $invoice->supplier->name ?? '-' }}</div>
                    </div>
                </td>
                <td class="col-spacer"></td>
                <td class="col-left">
                    <div class="detail-row">
                        <div class="detail-label">تاريخ الفاتورة</div>
                        <div class="detail-value  ">{{ $invoice->invoice_date instanceof \Carbon\Carbon ? $invoice->invoice_date->format('Y-m-d') : $invoice->invoice_date }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">الحالة</div>
                        <div class="detail-value">
                            @php
                                $statusMap = [
                                    'pending' => ['غير مدفوعة', 'badge-warning'],
                                    'paid' => ['مدفوعة', 'badge-success'],
                                    'partially_paid' => ['مدفوعة جزئياً', 'badge-info'],
                                ];
                                $status = $statusMap[$invoice->status] ?? [$invoice->status, 'badge-secondary'];
                            @endphp
                            <span class="badge {{ $status[1] }}">{{ $status[0] }}</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<h3 style="font-size: 14px; color: #004274; margin: 20px 0 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">الأصناف</h3>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>الصنف</th>
            <th class="text-center">الكمية</th>
            <th class="text-center">السعر الإفرادي ({{ currency_symbol() }})</th>
            <th class="text-center">الإجمالي ({{ currency_symbol() }})</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $index => $item)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $item->item_name }}</td>
            <td class="text-center  ">{{ $item->quantity }}</td>
            <td class="text-center  ">{{ number_format($item->unit_price, 2) }}</td>
            <td class="text-center   font-bold text-navy">{{ number_format($item->total_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table style="width: 100%; border: none; border-collapse: collapse; margin-top: 15px;">
    <tr>
        <td style="width: 60%;">
            @if($invoice->notes)
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; border-radius: 8px;">
                <div style="font-size: 10px; font-weight: bold; color: #004274; margin-bottom: 5px;">ملاحظات الفاتورة</div>
                <div style="font-size: 11px;">{{ $invoice->notes }}</div>
            </div>
            @endif
        </td>
        <td style="width: 40%; padding-right: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #e2e8f0; font-weight: bold;">الإجمالي الكلي</td>
                    <td style="padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: left; font-weight: bold; font-family: monospace; font-size: 14px; color: #004274;">{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #e2e8f0; font-weight: bold; color: #15803d;">المدفوع</td>
                    <td style="padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: left; font-weight: bold; font-family: monospace; font-size: 14px; color: #15803d;">{{ number_format($invoice->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; font-weight: bold; color: #b91c1c;">المتبقي (المديونية)</td>
                    <td style="padding: 8px; text-align: left; font-weight: bold; font-family: monospace; font-size: 14px; color: #b91c1c;">{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="signatures-table avoid-break">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">أعده / مسؤول التغذية</div>
            <div class="sign-name">{{ $exportUser ?? '' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">توقيع المستلم (المخزن)</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">توقيع المورد</div>
        </td>
    </tr>
</table>
@endsection
