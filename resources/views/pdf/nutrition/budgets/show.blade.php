@extends('pdf.layouts.master')

@section('content')

<div class="detail-card">
    <div class="detail-card-header">ملخص ميزانية قسم التغذية</div>
    <div class="detail-card-body">
        <table class="two-col-table">
            <tr>
                <td class="col-right">
                    <div class="detail-row">
                        <div class="detail-label">الفترة</div>
                        <div class="detail-value  ">شهر {{ $budget->month }} / سنة {{ $budget->year }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">بواسطة (المنشئ)</div>
                        <div class="detail-value">{{ $budget->creator->name ?? '-' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">عدد المشتركين</div>
                        <div class="detail-value  ">{{ $budget->subscribers_count }} مشترك</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">عدد الأيام</div>
                        <div class="detail-value  ">{{ $budget->days_count }} يوم</div>
                    </div>
                </td>
                <td class="col-spacer"></td>
                <td class="col-left">
                    <div class="detail-row">
                        <div class="detail-label">إجمالي الميزانية</div>
                        <div class="detail-value large   text-navy">{{ number_format($budget->total_amount, 2) }} ر.ي</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">الحالة</div>
                        <div class="detail-value">
                            @php
                                $statusMap = [
                                    'draft' => ['مسودة', 'badge-secondary'],
                                    'submitted' => ['قيد المراجعة', 'badge-warning'],
                                    'approved' => ['معتمدة', 'badge-success'],
                                    'rejected' => ['مرفوضة', 'badge-danger'],
                                ];
                                $status = $statusMap[$budget->status] ?? [$budget->status, 'badge-secondary'];
                            @endphp
                            <span class="badge {{ $status[1] }}">{{ $status[0] }}</span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">التكلفة اليومية للطالب</div>
                        <div class="detail-value   text-success">{{ number_format($budget->cost_per_student, 2) }} ر.ي</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

{{-- Supplier Summary Card in PDF --}}
@php
    $supplierTotals = collect($budget->lines)->groupBy('supplier_name')->map(function ($lines) {
        return $lines->sum('total');
    });
@endphp
@if($supplierTotals->count() > 0)
<h3 style="font-size: 14px; color: #004274; margin: 20px 0 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">إجمالي المبالغ حسب المورد</h3>
<table class="data-table mb-4">
    <thead>
        <tr>
            <th>اسم المورد</th>
            <th class="text-center">إجمالي المبلغ (ر.ي)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($supplierTotals as $supplierName => $total)
            @if(!empty($supplierName))
            <tr>
                <td class="font-bold">{{ $supplierName }}</td>
                <td class="text-center font-bold   text-navy">{{ number_format($total, 2) }}</td>
            </tr>
            @endif
        @endforeach
    </tbody>
</table>
@endif

<h3 style="font-size: 14px; color: #004274; margin: 20px 0 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">تفاصيل بنود الميزانية</h3>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>الصنف</th>
            <th class="text-center">المورد</th>
            <th class="text-center">الكمية اليومية</th>
            <th class="text-center">عدد الأيام</th>
            <th class="text-center">سعر الوحدة (ر.ي)</th>
            <th class="text-center">الإجمالي (ر.ي)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($budget->lines as $index => $item)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $item->item_name }}</td>
            <td class="text-center">{{ $item->supplier_name ?: '-' }}</td>
            <td class="text-center  ">{{ $item->quantity }}</td>
            <td class="text-center  ">{{ $item->days }}</td>
            <td class="text-center  ">{{ number_format($item->unit_price, 2) }}</td>
            <td class="text-center font-bold   text-navy">{{ number_format($item->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6" class="text-left">المجموع الكلي:</th>
            <th class="text-center font-black   text-success" style="font-size: 14px;">{{ number_format($budget->total_amount, 2) }} ر.ي</th>
        </tr>
    </tfoot>
</table>

<table class="signatures-table avoid-break" style="margin-top: 40px;">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">أعده / مشرف التغذية</div>
            <div class="sign-name">{{ $budget->creator->name ?? '-' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">مدير المركز</div>
            <div class="sign-name">
                @php
                      $center_manager = \App\Models\User::role('center-manager')->where('center_id',$budget->center_id)->get();
                @endphp
                {{ $center_manager->count() ===1 ? $center_manager->first()->name : '' }}
            </div>
        </td>
        
    </tr>
</table>
@endsection
