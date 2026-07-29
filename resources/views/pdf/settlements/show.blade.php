
@extends('pdf.layouts.master')

@section('content')


{{-- Overview Stats --}}
<table class="stats-row" style="margin-bottom: 25px;">
    <tr>
        <td class="stat-card">
            <div class="stat-label">الرصيد الافتتاحي الكلي</div>
            <div class="stat-value">{{ number_format($settlement->total_budget, 2) }} <span class="stat-unit">ر.ي</span></div>
        </td>
        <td class="stat-card" style="border-bottom: 3px solid #b91c1c;">
            <div class="stat-label">إجمالي المنصرف</div>
            <div class="stat-value text-danger">{{ number_format($settlement->total_spent, 2) }} <span class="stat-unit">ر.ي</span></div>
        </td>
        <td class="stat-card" style="border-bottom: 3px solid #15803d;">
            <div class="stat-label">الرصيد الختامي المتبقي</div>
            <div class="stat-value text-success">{{ number_format($settlement->total_remaining, 2) }} <span class="stat-unit">ر.ي</span></div>
        </td>
        <td class="stat-card">
            <div class="stat-label">عدد الصناديق</div>
            <div class="stat-value">{{ $settlement->details->count() }}</div>
        </td>
    </tr>
</table>

{{-- Detailed Funds Breakdown --}}
<h3 style="font-size: 14px; color: #004274; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">حركة الصناديق والعمليات التفصيلية</h3>

@foreach($settlement->details as $detail)
    @php
        $fundVouchers = $vouchers->filter(function($v) use ($detail) {
            return $v->fund_id == $detail->fund_id || ($v->type == 'transfer' && $v->target_fund_id == $detail->fund_id);
        })->sortByDesc('created_at');
    @endphp
    
    <div class="detail-card avoid-break">
        <div class="detail-card-header" style="background: #fafbfc; padding: 10px 15px;">
            <table style="width: 100%; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 40%; border: none;">
                        <span style="font-size: 13px; font-weight: bold; color: #004274;">{{ $detail->fund->name }}</span><br>
                        <span style="font-size: 9px; color: #64748b;margin-bottom: 4px;">{{ $fundVouchers->count() }} حركة مالية مسجلة</span>
                    </td>
                    <td style="width: 15%; text-align: center; border: none;">
                        <div style="font-size: 8px; color: #64748b;margin-bottom: 4px;">الرصيد الافتتاحي</div>
                        <div style="font-size: 11px; font-weight: bold; color: #334155;">{{ number_format($detail->opening_balance, 2) }}</div>
                    </td>
                    <td style="width: 15%; text-align: center; border: none;">
                        <div style="font-size: 8px; color: #15803d;margin-bottom: 4px;">إجمالي المقبوضات</div>
                        <div style="font-size: 11px; font-weight: bold; color: #15803d;">+{{ number_format($detail->total_income, 2) }}</div>
                    </td>
                    <td style="width: 15%; text-align: center; border: none;">
                        <div style="font-size: 8px; color: #b91c1c;margin-bottom: 4px;">إجمالي المصروفات</div>
                        <div style="font-size: 11px; font-weight: bold; color: #b91c1c;">-{{ number_format($detail->total_expense, 2) }}</div>
                    </td>
                    <td style="width: 15%; text-align: left; border: none;">
                            <div style="font-size: 8px; color: #64748b; text-align: center;margin-bottom: 4px;">الرصيد الختامي</div>
<div
    style="font-size: 11px; font-weight: bold; text-align: center; color: {{ $detail->closing_balance < 0 ? '#dc2626' : '#000000' }};">
    {{ number_format($detail->closing_balance, 2) }}
</div>                        </div>
                    </td>
                </tr>
            </table>
        </div>

        @if($fundVouchers->count() > 0)
            <table class="data-table" style="margin-bottom: 0; font-size: 9px;">
                <thead>
                    <tr>
                        <th>رقم السند</th>
                        <th>التاريخ</th>
                        <th>النوع</th>
                        <th>المبلغ (ر.ي)</th>
                        <th>مناولة</th>
                        <th>البيان</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fundVouchers as $voucher)
                        @php
                            $isIncoming = false;
                            if (in_array($voucher->type, ['receipt', 'sales_invoice'])) {
                                $isIncoming = true;
                            } elseif ($voucher->type == 'transfer' && $voucher->target_fund_id == $detail->fund_id) {
                                $isIncoming = true;
                            }
                            
                            $types = [
                                'receipt' => 'سند قبض',
                                'payment' => 'سند صرف',
                                'transfer' => 'سند تحويل',
                                'salary' => 'مسير رواتب',
                            ];
                            $typeLabel = $types[$voucher->type] ?? $voucher->type;
                        @endphp
                        <tr>
                            <td class="font-mono text-navy font-bold">{{ $voucher->voucher_number }}</td>
                            <td class="font-mono text-sm text-muted">{{ $voucher->date instanceof \Carbon\Carbon ? $voucher->date->format('Y-m-d') : $voucher->date }}</td>
                            <td>{{ $typeLabel }}</td>
                            <td class="font-mono font-bold {{ $isIncoming ? 'text-success' : 'text-danger' }}" dir="ltr">
                                {{ $isIncoming ? '+' : '-' }}{{ number_format($voucher->amount, 2) }}
                            </td>
                            <td class="text-sm">
                                @if($voucher->type == 'transfer')
                                    @if($isIncoming) من: {{ $voucher->fund->name }}
                                    @else إلى: {{ $voucher->targetFund->name }}
                                    @endif
                                @else
                                    {{ $voucher->payee_or_payer }}
                                @endif
                            </td>
                            <td class="text-xs" style="max-width: 150px; overflow: hidden;">{{ \Illuminate\Support\Str::limit($voucher->description, 50) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 15px; text-align: center; color: #94a3b8; font-size: 10px;">
                لم يتم تسجيل أي عمليات مالية على هذا الصندوق خلال هذا الشهر.
            </div>
        @endif
    </div>
@endforeach

<table class="signatures-table">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">أعده / المحاسب المالي</div>
            <div class="sign-name">{{ $settlement->submitter->name ?? '-' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">راجعه واعتمده / مدير المركز</div>
            <div class="sign-name">-</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">الاعتماد النهائي / مدير قسم المراكز الطلابية</div>
            <div class="sign-name">{{ $settlement->approver->name ?? '-' }}</div>
        </td>
    </tr>
</table>
@endsection
