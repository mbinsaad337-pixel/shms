@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>اسم النادي</th>
            <th>المركز</th>
            <th>التصنيف</th>
            <th class="text-center">عدد الأعضاء</th>
            <th class="text-center">الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $club)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $club->name }}</td>
            <td>{{ $club->center->name ?? '---' }}</td>
            <td>{{ $club->category }}</td>
            <td class="text-center font-mono font-bold">{{ $club->members_count ?? $club->members->count() }}</td>
            <td class="text-center">
                @php
                    $statusMap = [
                        'active' => ['نشط', 'badge-success'],
                        'inactive' => ['غير نشط', 'badge-danger'],
                    ];
                    $status = $statusMap[$club->status] ?? [$club->status, 'badge-secondary'];
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
            <div class="sign-name">
                @php
                      $Activty_manager = \App\Models\User::role('social-manager')->where('center_id',$club->center_id)->first();
                @endphp
                {{ $Activty_manager->name ?? '---' }}
            </div>
        </td>
        
        <td>
            <div class="sign-line"></div>
            <div class="sign-title"> مدير المركز</div>
            <div class="sign-name">
                @php
                      $center_manager = \App\Models\User::role('center-manager')->where('center_id',$club->center_id)->first();
                @endphp
                {{ $center_manager->name ?? '---' }}
            </div>
        </td>
    </tr>
</table>
@endsection
