@extends('pdf.layouts.master')

@section('content')
<div class="detail-card">
    <div class="detail-card-header">تفاصيل مصروف المركز</div>
    <div class="detail-card-body">
        <table class="two-col-table">
            <tr>
                <td class="col-right">
                    <div class="detail-row"><div class="detail-label">المركز</div><div class="detail-value">{{ $expense->center?->name ?? '—' }}</div></div>
                    <div class="detail-row"><div class="detail-label">نوع المصروف</div><div class="detail-value">{{ $expense->type_label }}</div></div>
                    <div class="detail-row"><div class="detail-label">الفترة</div><div class="detail-value">{{ str_pad($expense->month, 2, '0', STR_PAD_LEFT) }} / {{ $expense->year }}</div></div>
                </td>
                <td class="col-spacer"></td>
                <td class="col-left">
                    <div class="detail-row"><div class="detail-label">المبلغ</div><div class="detail-value large text-navy">{{ number_format($expense->amount, 2) }} {{ $expense->currency_symbol }}</div></div>
                    <div class="detail-row"><div class="detail-label">العملة</div><div class="detail-value">{{ $expense->currency_label }}</div></div>
                    <div class="detail-row"><div class="detail-label">الحالة</div><div class="detail-value">{{ $expense->status_label }}</div></div>
                    <div class="detail-row"><div class="detail-label">تاريخ الاستحقاق</div><div class="detail-value">{{ optional($expense->due_date)->format('Y-m-d') ?? '—' }}</div></div>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="detail-card" style="margin-top: 16px;">
    <div class="detail-card-header">بيانات الدفع والمرفق</div>
    <div class="detail-card-body">
        <table class="two-col-table"><tr><td class="col-right"><div class="detail-row"><div class="detail-label">تاريخ الدفع</div><div class="detail-value">{{ optional($expense->payment_date)->format('Y-m-d') ?? 'لم يتم الدفع' }}</div></div></td><td class="col-spacer"></td><td class="col-left"><div class="detail-row"><div class="detail-label">الإيصال</div><div class="detail-value">{{ $expense->receipt ? 'مرفق (' . strtoupper($expense->receipt_type ?: 'ملف') . ')' : 'لا يوجد مرفق' }}</div></div></td></tr></table>
        @if($expense->notes)<div class="detail-row" style="margin-top: 14px;"><div class="detail-label">ملاحظات</div><div class="detail-value">{{ $expense->notes }}</div></div>@endif
    </div>
</div>
@endsection
