@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>الطالب</th>
            <th>المركز</th>
            <th>العنوان</th>
            <th class="text-center">التاريخ</th>
            <th class="text-center">الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $commitment)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $commitment->student->name_ar ?? '---' }}</td>
            <td>{{ $commitment->student->center->name ?? '---' }}</td>
            <td>{{ $commitment->title ?? 'تعهد' }}</td>
            <td class="text-center font-mono text-sm">{{ $commitment->date->format('Y/m/d') }}</td>
            <td class="text-center">
                <span class="badge {{ $commitment->status == 'active' ? 'badge-success' : 'badge-secondary' }}">
                    {{ $commitment->status == 'active' ? 'نشط' : 'منتهي' }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="signatures-table">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">مشرف الطلاب</div>
            <div class="sign-name">
                 @php
                      $housing_manager = \App\Models\User::role('housing-manager')->where('center_id',$commitment->student->center_id)->get();
                @endphp
                {{ $housing_manager->count() ===1 ? $housing_manager->first()->name : '' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">مدير المركز</div>
            <div class="sign-name">
                @php
                      $center_manager = \App\Models\User::role('center-manager')->where('center_id',$commitment->student->center_id)->get();
                @endphp
                {{ $center_manager->count() ===1 ? $center_manager->first()->name : '' }}
            </div>
        </td>
    </tr>
</table>
@endsection
