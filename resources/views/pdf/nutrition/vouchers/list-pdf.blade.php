@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>اسم المشرف</th>
            <th>المركز</th>
            <th>نوع العهدة</th>
            <th>المبلغ (ر.ي)</th>
            <th class="text-center">تاريخ الصرف</th>
            <th class="text-center">الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $voucher)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $voucher->supervisor->name ?? '---' }}</td>
            <td>{{ $voucher->center->name ?? '---' }}</td>
            <td>{{ $voucher->type == 'cash' ? 'نقدية' : 'عينية' }}</td>
            <td class="font-bold   text-navy">{{ number_format($voucher->amount, 2) }}</td>
            <td class="text-center   text-sm">{{ $voucher->date instanceof \Carbon\Carbon ? $voucher->date->format('Y/m/d') : $voucher->date }}</td>
            <td class="text-center">
                @php
                    $statusMap = [
                        'draft' => ['مسودة', 'badge-secondary'],
                        'active' => ['فعالة', 'badge-success'],
                        'settled' => ['تمت التصفية', 'badge-info'],
                    ];
                    $status = $statusMap[$voucher->status] ?? [$voucher->status, 'badge-secondary'];
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
