@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>اسم الفعالية</th>
            <th>النادي المنظم</th>
            <th>المكان</th>
            <th class="text-center">تاريخ البداية</th>
            <th class="text-center">عدد المشاركين</th>
            <th class="text-center">الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $activity)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $activity->name }}</td>
            <td>{{ $activity->club->name ?? '---' }}</td>
            <td>{{ $activity->location }}</td>
            <td class="text-center font-mono text-sm">{{ $activity->start_date instanceof \Carbon\Carbon ? $activity->start_date->format('Y/m/d') : $activity->start_date }}</td>
            <td class="text-center font-mono font-bold">{{ $activity->participants->count() }}</td>
            <td class="text-center">
                @php
                    $statusMap = [
                        'planned' => ['مخطط لها', 'badge-info'],
                        'active' => ['جارية', 'badge-success'],
                        'finished' => ['منتهية', 'badge-secondary'],
                        'cancelled' => ['ملغاة', 'badge-danger'],
                    ];
                    $status = $statusMap[$activity->status] ?? [$activity->status, 'badge-secondary'];
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
            <div class="sign-title">أعده / مسؤول الأنشطة</div>
            <div class="sign-name">{{ $exportUser ?? '' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">راجعه / المسؤول الإداري</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">اعتمده / مدير المركز</div>
        </td>
    </tr>
</table>
@endsection
