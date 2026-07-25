@extends('pdf.layouts.master')

@section('content')
@php $totalGlobal = 0; @endphp

@foreach($data->groupBy('center_id') as $centerId => $funds)
    @php $centerName = $funds->first()->center->name; @endphp

    <div class="detail-card avoid-break" style="margin-bottom: 20px;">
        <div class="detail-card-header" style="background: #004274; color: #ffffff; font-size: 12px;">
            {{ $centerName }}
        </div>
        <table class="data-table" style="margin-bottom: 0;">
            <thead>
                <tr>
                    <th>اسم الصندوق</th>
                    <th class="text-center">الرصيد المعتمد (ر.ي)</th>
                    <th class="text-center">الرصيد الحالي (ر.ي)</th>
                    <th class="text-center">رصيد التصفية (ر.ي)</th>
                    <th class="text-center">الفترة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($funds as $fund)
                    @php
                        $approvedBalance = $fund->budgetItems->sum('approved_amount');
                        $settlementDetail = $settlementBalances[$fund->id] ?? null;
                        $settlementClosing = $settlementDetail ? $settlementDetail->closing_balance : null;
                        $totalGlobal += $fund->balance;
                    @endphp
                    <tr>
                        <td class="font-bold">{{ $fund->name }}</td>
                        <td class="text-center font-bold font-mono text-navy">{{ number_format($approvedBalance, 2) }}</td>
                        <td class="text-center font-bold font-mono text-success">{{ number_format($fund->balance, 2) }}</td>
                        <td class="text-center font-mono">
                            @if($settlementClosing !== null)
                                <span class="font-bold" style="color: #c2410c;">{{ number_format($settlementClosing, 2) }}</span>
                            @else
                                <span class="text-muted">لا تصفية</span>
                            @endif
                        </td>
                        <td class="text-center font-mono text-sm">{{ $month }} / {{ $year }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endforeach

{{-- Grand Total --}}
<div style="background: #004274; padding: 15px; border-radius: 8px; text-align: center; margin-top: 10px;">
    <div style="font-size: 12px; font-weight: bold; color: #ffffff; margin-bottom: 5px;">إجمالي السيولة النقدية بالنظام</div>
    <div style="font-size: 22px; font-weight: bold; color: #D4A044;">{{ number_format($totalGlobal, 2) }} <span style="font-size: 11px; color: #ffffff;">ريال يمني</span></div>
</div>

<table class="signatures-table">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">أعده / المحاسب المالي</div>
            <div class="sign-name">{{ $exportUser ?? '' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">راجعه / المدير المالي</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">اعتمده / المدير العام</div>
        </td>
    </tr>
</table>
@endsection
