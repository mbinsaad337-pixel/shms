@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>رقم الفاتورة</th>
            <th>المورد</th>
            <th>المبلغ (ر.ي)</th>
            <th class="text-center">تاريخ الفاتورة</th>
            <th class="text-center">الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $invoice)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold font-mono">{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->supplier->name ?? '---' }}</td>
            <td class="font-bold font-mono text-navy">{{ number_format($invoice->total_amount, 2) }}</td>
            <td class="text-center font-mono text-sm">{{ $invoice->invoice_date instanceof \Carbon\Carbon ? $invoice->invoice_date->format('Y/m/d') : $invoice->invoice_date }}</td>
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
    </tbody>
</table>

<table class="signatures-table">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">أعده / مسؤول التغذية</div>
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
