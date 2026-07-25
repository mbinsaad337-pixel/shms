@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>كود الأصل</th>
            <th>اسم الأصل</th>
            <th>المركز</th>
            <th>التصنيف</th>
            <th class="text-center">الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $asset)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-mono font-bold text-navy">{{ $asset->code }}</td>
            <td class="font-bold">{{ $asset->name }}</td>
            <td>{{ $asset->center->name ?? '---' }}</td>
            <td>{{ $asset->category }}</td>
            <td class="text-center">
                @php
                    $statusMap = [
                        'good' => ['جيد', 'badge-success'],
                        'needs_maintenance' => ['يحتاج صيانة', 'badge-warning'],
                        'damaged' => ['تالف', 'badge-danger'],
                        'disposed' => ['مستبعد', 'badge-secondary'],
                    ];
                    $status = $statusMap[$asset->status] ?? [$asset->status, 'badge-secondary'];
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
            <div class="sign-title">أعده / مسؤول العهد والأصول</div>
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
