
@extends('pdf.layouts.master')

@section('content')

use Illuminate\Support\Facades\DB;




<div class="detail-card">
    <div class="detail-card-header">تفاصيل النادي</div>
    <div class="detail-card-body">
        <table class="two-col-table">
            <tr>
                <td class="col-right">
                    <div class="detail-row">
                        <div class="detail-label">اسم النادي</div>
                        <span class="detail-value text-navy"><h3>{{ $club->name }}</h3></span>
                    </div>
                    
                </td>
                <td class="col-spacer"></td>
                <td class="col-left">
                    <div class="detail-row">
                        <div class="detail-label">المركز التابع له</div>
                        <div class="detail-value">{{ $club->center->name ?? '---' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">إجمالي الأعضاء</div>
                        <div class="detail-value  ">{{ $club->members->count() }} عضو</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<h3 style="font-size: 14px; color: #004274; margin: 20px 0 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">قائمة الأعضاء المنضمين</h3>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>اسم الطالب</th>
            <th>الصفة في النادي</th>
            <th class="text-center">تاريخ الانضمام</th>
        </tr>
    </thead>
    <tbody>
        @foreach($club->members as $index => $member)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $member->student->name_ar ?? '---' }}</td>
            <td>
                @php
                    $roleMap = [
                        'leader' => ['قائد النادي', 'badge-primary'],
                        'deputy' => ['نائب القائد', 'badge-info'],
                        'coordinator' => ['منسق', 'badge-warning'],
                        'member' => ['عضو', 'badge-secondary'],
                    ];
                    $role = $roleMap[$member->role] ?? [$member->role, 'badge-secondary'];
                @endphp
                <span class="badge {{ $role[1] }}">{{ $role[0] }}</span>
            </td>
            <td class="text-center   text-sm">{{ $member->joined_at instanceof \Carbon\Carbon ? $member->joined_at->format('Y/m/d') : $member->joined_at }}</td>
        </tr>
        @endforeach
        
        @if($club->members->count() == 0)
        <tr>
            <td colspan="5" class="text-center text-muted" style="padding: 20px;">لا يوجد أعضاء في هذا النادي حالياً</td>
        </tr>
        @endif
    </tbody>
</table>

<table class="signatures-table avoid-break">
    <tr>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title"> مسؤول الأنشطة </div>
            <div class="sign-name">
                @php
                      $Activty_manager = \App\Models\User::role('social-manager')->where('center_id',$club->center_id)->get();
                @endphp
                {{ $Activty_manager->count() ===1 ? $Activty_manager->first()->name : null }}
            </div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title"> قائد النادي</div>
            <div class="sign-name">
                @php
                    $leader = $club->members->where('role','رئيس النادي')->first();
                @endphp
                {{ $leader ? $leader->student->name_ar : null }}
            </div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title"> مدير المركز</div>
            <div class="sign-name">
                @php
                      $center_manager = \App\Models\User::role('center-manager')->where('center_id',$club->center_id)->get();
                @endphp
                {{ $center_manager->count() ===1 ? $center_manager->first()->name : null }}
            </div>
        </td>
    </tr>
</table>
@endsection
