@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>رقم السند</th>
            <th>المركز</th>
            <th class="text-center">النوع</th>
            <th class="text-center">المبلغ ({{ currency_symbol() }})</th>
            <th>الصندوق</th>
            <th>البيان</th>
            <th class="text-center">التاريخ</th>
        </tr>
    </thead>
    <tbody>
        @php $totalReceipts = 0; $totalPayments = 0; @endphp
        @foreach($data as $index => $voucher)
        @php
            if(in_array($voucher->type, ['receipt', 'sales_invoice'])) $totalReceipts += $voucher->amount;
            else $totalPayments += $voucher->amount;
        @endphp
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="  font-bold text-navy">{{ $voucher->voucher_number }}</td>
            <td>{{ $voucher->center->name }}</td>
            <td class="text-center">
                @php
                    $typeMap = [
                        'receipt' => ['قبض', 'badge-success'],
                        'payment' => ['صرف', 'badge-danger'],
                        'transfer' => ['تحويل', 'badge-info'],
                        'salary' => ['رواتب', 'badge-warning'],
                    ];
                    $type = $typeMap[$voucher->type] ?? [$voucher->type, 'badge-secondary'];
                @endphp
                <span class="badge {{ $type[1] }}">{{ $type[0] }}</span>
            </td>
            <td class="text-center font-bold  ">{{ number_format($voucher->amount, 2) }}</td>
            <td>{{ $voucher->fund->name ?? '-' }}</td>
            <td class="text-sm" style="max-width: 150px; overflow: hidden;">{{ \Illuminate\Support\Str::limit($voucher->description, 50) }}</td>
            <td class="text-center   text-sm">{{ $voucher->date instanceof \Carbon\Carbon ? $voucher->date->format('Y-m-d') : $voucher->date }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Totals Summary --}}
<table style="width: 100%; margin-top: 15px; border: none; border-collapse: collapse;">
    <tr>
        <td style="width: 50%; border: none; padding: 8px;">
            <div style="background: #dcfce7; border: 1px solid #bbf7d0; border-radius: 6px; padding: 10px; text-align: center;">
                <div style="font-size: 9px; color: #15803d; font-weight: bold; margin-bottom: 4px;">إجمالي المقبوضات</div>
                <div style="font-size: 16px; font-weight: bold; color: #15803d;">{{ number_format($totalReceipts, 2) }} {{ currency_symbol() }}</div>
            </div>
        </td>
        <td style="width: 50%; border: none; padding: 8px;">
            <div style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 6px; padding: 10px; text-align: center;">
                <div style="font-size: 9px; color: #b91c1c; font-weight: bold; margin-bottom: 4px;">إجمالي المصروفات</div>
                <div style="font-size: 16px; font-weight: bold; color: #b91c1c;">{{ number_format($totalPayments, 2) }} {{ currency_symbol() }}</div>
            </div>
        </td>
    </tr>
</table>

<table class="signatures-table avoid-break">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">  المسؤول المالي</div>
<div class="sign-name">
                    @php
                      $financial_manager = \App\Models\User::role('financial-manager')->where('center_id',$budget->center_id)->get();
                @endphp
                {{ $financial_manager->count() ===1 ? $financial_manager->first()->name : '' }}
            </div>        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">  مدير المركز</div>
          <div class="sign-name">
                    @php
                      $center_manager = \App\Models\User::role('center-manager')->where('center_id',$budget->center_id)->get();
                @endphp
                {{ $center_manager->count() ===1 ? $center_manager->first()->name : '' }}
            </div>        </td>
        <td>
            <div class="sign-line"></div>
<div class="sign-title">المستفيد </div>
        </td>
            
    </tr>
</table>
@endsection
