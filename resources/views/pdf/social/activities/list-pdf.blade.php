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
            th
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
            td
            <td class="text-center font-mono font-bold">{{ $activity->participants->count() }}</td>
            
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="signatures-table">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title"> مسؤول الأنشطة</div>
            <div class="sign-name">
                @php
                      $Activty_manager = \App\Models\User::role('social-manager')->where('center_id',$activity->center_id)->get();
                @endphp
                {{ $Activty_manager->count() ===1 ? $Activty_manager->first()->name : '' }}
            </div>
        </td>
        
        <td>
            <div class="sign-line"></div>
            <div class="sign-title"> مدير المركز</div>
            <div class="sign-name">
                @php
                      $center_manager = \App\Models\User::role('center-manager')->where('center_id',$activity->center_id)->get();
                @endphp
                {{ $center_manager->count() ===1 ? $center_manager->first()->name : '' }}
            </div>
        </td>
    </tr>
</table>
@endsection
