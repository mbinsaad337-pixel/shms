@extends('pdf.layouts.master')

@section('content')

<div class="detail-card">
    <div class="detail-card-header">ملخص التصفية المالية</div>
    <div class="detail-card-body">
        <table class="two-col-table">
            <tr>
                <td class="col-right">
                    <div class="detail-row">
                        <div class="detail-label">الفترة</div>
                        <div class="detail-value font-mono">شهر {{ $settlement->month }} / سنة {{ $settlement->year }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">تاريخ التصفية</div>
                        <div class="detail-value font-mono text-sm">{{ $settlement->created_at->format('Y-m-d') }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">بواسطة</div>
                        <div class="detail-value">{{ $settlement->creator->name ?? '-' }}</div>
                    </div>
                </td>
                <td class="col-spacer"></td>
                <td class="col-left">
                    <div class="detail-row">
                        <div class="detail-label">الحالة</div>
                        <div class="detail-value">
                            @php
                                $statusMap = [
                                    'draft' => ['مسودة', 'badge-secondary'],
                                    'submitted' => ['قيد المراجعة', 'badge-warning'],
                                    'confirmed' => ['مؤكدة', 'badge-info'],
                                    'approved' => ['معتمدة', 'badge-success'],
                                    'rejected' => ['مرفوضة', 'badge-danger'],
                                ];
                                $status = $statusMap[$settlement->status] ?? [$settlement->status, 'badge-secondary'];
                            @endphp
                            <span class="badge {{ $status[1] }}">{{ $status[0] }}</span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">تاريخ الاعتماد</div>
                        <div class="detail-value font-mono text-sm">{{ $settlement->approved_at ? $settlement->approved_at->format('Y-m-d H:i') : 'لم تعتمد بعد' }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

{{-- Overview Stats --}}
<table class="stats-row" style="margin-bottom: 25px;">
    <tr>
        <td class="stat-card">
            <div class="stat-label">الرصيد الافتتاحي الكلي</div>
            <div class="stat-value text-navy">{{ number_format($settlement->total_budget, 2) }} <span class="stat-unit">ر.ي</span></div>
        </td>
        <td class="stat-card" style="border-bottom: 3px solid #b91c1c;">
            <div class="stat-label">إجمالي المنصرف</div>
            <div class="stat-value text-danger">{{ number_format($settlement->total_spent, 2) }} <span class="stat-unit">ر.ي</span></div>
        </td>
        <td class="stat-card" style="border-bottom: 3px solid #15803d;">
            <div class="stat-label">الرصيد الختامي المتبقي</div>
            <div class="stat-value text-success">{{ number_format($settlement->total_remaining, 2) }} <span class="stat-unit">ر.ي</span></div>
        </td>
    </tr>
</table>

<h3 style="font-size: 14px; color: #004274; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">تفاصيل حركة الصناديق خلال الشهر</h3>

<table class="data-table">
    <thead>
        <tr>
            <th>اسم الصندوق</th>
            <th class="text-center">الرصيد الافتتاحي (ر.ي)</th>
            <th class="text-center">المقبوضات (+)</th>
            <th class="text-center">المنصرف (-)</th>
            <th class="text-center">الرصيد الختامي (ر.ي)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($settlement->details as $detail)
        <tr>
            <td class="font-bold">{{ $detail->fund->name ?? '-' }}</td>
            <td class="text-center font-mono font-bold">{{ number_format($detail->opening_balance, 2) }}</td>
            <td class="text-center font-mono text-success">+{{ number_format($detail->total_income, 2) }}</td>
            <td class="text-center font-mono text-danger">-{{ number_format($detail->total_expense, 2) }}</td>
            <td class="text-center font-mono font-bold text-navy">{{ number_format($detail->closing_balance, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background: #fafbfc; font-weight: bold;">
            <td class="text-left">الإجمالي الكلي:</td>
            <td class="text-center font-mono">{{ number_format($settlement->details->sum('opening_balance'), 2) }}</td>
            <td class="text-center font-mono text-success">+{{ number_format($settlement->details->sum('total_income'), 2) }}</td>
            <td class="text-center font-mono text-danger">-{{ number_format($settlement->details->sum('total_expense'), 2) }}</td>
            <td class="text-center font-mono text-navy">{{ number_format($settlement->details->sum('closing_balance'), 2) }}</td>
        </tr>
    </tfoot>
</table>

@if($settlement->notes)
<div class="detail-card avoid-break" style="margin-top: 20px;">
    <div class="detail-card-header">ملاحظات والتسويات اليدوية</div>
    <div class="detail-card-body">
        <p style="font-size: 11px;">{{ $settlement->notes }}</p>
    </div>
</div>
@endif

<table class="signatures-table avoid-break">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">أعده / المحاسب</div>
            <div class="sign-name">{{ $settlement->creator->name ?? '-' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">راجعه / مدير المركز</div>
            <div class="sign-name">{{ $settlement->confirmer->name ?? '-' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">اعتمده / المدير العام</div>
            <div class="sign-name">{{ $settlement->approver->name ?? '-' }}</div>
        </td>
    </tr>
</table>
@endsection
