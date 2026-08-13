@extends('pdf.layouts.master')

@section('content')

<div class="detail-card">
    <div class="detail-card-header">ملخص الموازنة المطلوبة</div>
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
                        <div class="detail-value">{{ $budget->submitter->name ?? '-' }}</div>
                    </div>
                </td>
                <td class="col-spacer"></td>
                <td class="col-left">
                    <div class="detail-row">
                        <div class="detail-label">المبلغ الإجمالي للموازنة</div>
                        <div class="detail-value large   text-navy">{{ number_format($budget->total_amount, 2) }} ر.ي</div>
                    </div>
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
                                $status = $statusMap[$budget->status] ?? [$budget->status, 'badge-secondary'];
                            @endphp
                            <span class="badge {{ $status[1] }}">{{ $status[0] }}</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<h3 style="font-size: 14px; color: #004274; margin: 20px 0 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">تفاصيل بنود الموازنة</h3>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>الصندوق / البند</th>
            <th class="text-center">المبلغ المطلوب (ر.ي)</th>
            @if($budget->status == 'approved')
            <th class="text-center">المبلغ المعتمد (ر.ي)</th>
            @endif
            <th class="text-center">الرصيد الحالي للصندوق</th>
            <th class="text-center">المرفق (PDF)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($budget->items as $index => $item)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $item->fund->name }}</td>
            <td class="text-center font-bold   text-navy">{{ number_format($item->requested_amount, 2) }}</td>
            @if($budget->status == 'approved')
            <td class="text-center font-bold   text-success">{{ number_format($item->approved_amount, 2) }}</td>
            @endif
            <td class="text-center   text-muted">{{ number_format($item->fund->balance, 2) }}</td>
            <td class="text-center">
                @if($item->attachment_pdf)
                    <a href="{{ asset('storage/' . $item->attachment_pdf) }}" target="_blank" style="color: #dc2626; text-decoration: none; font-weight: bold;">عرض المرفق</a>
                @else
                    <span style="color: #9ca3af;">لا يوجد</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if($budget->notes)
<div class="detail-card avoid-break" style="margin-top: 20px;">
    <div class="detail-card-header">ملاحظات</div>
    <div class="detail-card-body">
        <p style="font-size: 11px;">{{ $budget->notes }}</p>
    </div>
</div>
@endif

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
<div class="sign-title">مدير قسم المراكز الطلابية</div>
            @php
                $super_admin = \App\Models\User::role('super-admin')->first();
            @endphp
            <div class="sign-name">{{ $super_admin ? $super_admin->name : '' }}</div>        </td>
    </tr>
</table>
@endsection
