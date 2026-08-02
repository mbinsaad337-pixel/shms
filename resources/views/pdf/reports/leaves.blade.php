@extends('pdf.layouts.master')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th>الطالب</th>
            <th>النوع</th>
            <th class="text-center">تاريخ / وقت المغادرة</th>
            <th class="text-center">تاريخ / وقت العودة</th>
            <th class="text-center">الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $leave)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">
                {{ $leave->student->name_ar ?? '---' }}
                @if($leave->submitted_by_student) <div style="font-size: 8px; color: #666;">(طلب الطالب)</div> @endif
            </td>
            <td class="text-center">
                @php $typeLabels = ['temporary'=>'مؤقت','vacation'=>'إجازة','medical'=>'طبي','lateness'=>'تأخير']; @endphp
                {{ $typeLabels[$leave->type] ?? $leave->type }}
            </td>
            <td class="text-center font-mono text-sm">
                {{ $leave->departure_date->format('Y/m/d') }}<br>
                {{ $leave->departure_time ?? '' }}
            </td>
            <td class="text-center font-mono text-sm">
                {{ $leave->expected_return_date ? $leave->expected_return_date->format('Y/m/d') : '---' }}<br>
                {{ $leave->expected_return_time ?? '' }}
            </td>
            <td class="text-center">
                @php
                    $stLabels = ['pending'=>'قيد الانتظار','approved'=>'موافق','rejected'=>'مرفوض','returned'=>'عاد','not_returned'=>'لم يعد'];
                @endphp
                {{ $stLabels[$leave->status] ?? $leave->status }}
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
                      $housing_manager = \App\Models\User::role('housing-manager')->where('center_id',$leave->student->center_id)->get();
                @endphp
                {{ $housing_manager->count() ===1 ? $housing_manager->first()->name : '' }}</div>
        </td>
        <td>
            <div class="sign-line"></div>
            <div class="sign-title">مدير المركز</div>
            <div class="sign-name">
                @php
                      $center_manager = \App\Models\User::role('center-manager')->where('center_id',$leave->student->center_id)->get();
                @endphp
                {{ $center_manager->count() ===1 ? $center_manager->first()->name : '' }}
            </div>
        </td>
    </tr>
</table>
@endsection
