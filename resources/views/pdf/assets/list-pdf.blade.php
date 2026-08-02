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
            <td class="  font-bold text-navy">{{ $asset->code }}</td>
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
            <div class="sign-title">مدير المركز</div>
            @php
                $center_manager = \App\Models\User::role('center-manager')->where('center_id', $asset->center_id)->first();
            @endphp
            <div class="sign-name">{{ $center_manager ? $center_manager->name : '' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">مدير قسم المراكز الطلابية</div>
            @php
                $super_admin = \App\Models\User::role('super-admin')->first();
            @endphp
            <div class="sign-name">{{ $super_admin ? $super_admin->name : '' }}</div>
        </td>
    </tr>
</table>
@endsection
